<?php

namespace App\Services;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignContent;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class NewsletterBrevoCampaignService
{
    public function __construct(
        private readonly NewsletterCampaignContentService $content,
        private readonly NewsletterBrevoService $contacts,
    ) {}

    public function sendNow(NewsletterCampaign $campaign): bool
    {
        return $this->deliver($campaign, NewsletterCampaign::STATUS_DRAFT);
    }

    public function retry(NewsletterCampaign $campaign): bool
    {
        return $this->deliver($campaign, NewsletterCampaign::STATUS_FAILED);
    }

    private function deliver(NewsletterCampaign $campaign, string $requiredStatus): bool
    {
        $campaign = DB::transaction(function () use ($campaign, $requiredStatus): NewsletterCampaign {
            $lockedCampaign = NewsletterCampaign::query()->lockForUpdate()->findOrFail($campaign->getKey());

            abort_unless($lockedCampaign->status === $requiredStatus, 422, 'Campaign is not eligible for this delivery action.');
            abort_if($lockedCampaign->contents()->count() === 0, 422, 'Campaign must contain at least one content item.');

            $lockedCampaign->forceFill([
                'status' => NewsletterCampaign::STATUS_SENDING,
                'brevo_status' => 'sending',
                'brevo_error' => null,
                'sent_at' => null,
            ])->save();

            return $lockedCampaign;
        });

        try {
            $configuration = $this->configuration();
            $this->contacts->assertCampaignPersonalizationReady();
            $campaign = $this->productionCampaign($campaign);

            if ($campaign->brevo_campaign_id === null) {
                $campaignId = $this->createCampaign($campaign, $configuration);
                $campaign->forceFill([
                    'brevo_campaign_id' => $campaignId,
                    'brevo_status' => 'created',
                ])->save();
            }

            $this->sendCampaign((int) $campaign->brevo_campaign_id, $configuration);

            $campaign->forceFill([
                'status' => NewsletterCampaign::STATUS_SENT,
                'brevo_status' => 'sent',
                'brevo_error' => null,
                'sent_at' => now(),
            ])->save();

            return true;
        } catch (Throwable $exception) {
            report($exception);

            $campaign->forceFill([
                'status' => NewsletterCampaign::STATUS_FAILED,
                'brevo_status' => 'failed',
                'brevo_error' => $this->safeError($exception, (string) config('services.brevo_newsletter.api_key')),
                'sent_at' => null,
            ])->save();

            return false;
        }
    }

    /**
     * @return array{api_key: string, base_url: string, list_id: int, sender_email: string, sender_name: string}
     */
    private function configuration(): array
    {
        $apiKey = trim((string) config('services.brevo_newsletter.api_key'));
        $baseUrl = rtrim((string) config('services.brevo_newsletter.base_url', 'https://api.brevo.com/v3'), '/');
        $listId = filter_var(config('services.brevo_newsletter.list_id'), FILTER_VALIDATE_INT);
        $senderEmail = trim((string) config('services.brevo_newsletter.sender_email'));
        $senderName = trim((string) config('services.brevo_newsletter.sender_name'));

        if ($apiKey === '' || $baseUrl === '' || $listId === false || $listId < 1 || ! filter_var($senderEmail, FILTER_VALIDATE_EMAIL) || $senderName === '') {
            throw new RuntimeException('Newsletter Brevo campaign configuration is incomplete.');
        }

        return ['api_key' => $apiKey, 'base_url' => $baseUrl, 'list_id' => (int) $listId, 'sender_email' => $senderEmail, 'sender_name' => $senderName];
    }

    private function productionCampaign(NewsletterCampaign $campaign): NewsletterCampaign
    {
        $campaign = $this->content->hydrate($campaign);
        $validContents = $campaign->contents->filter(function (NewsletterCampaignContent $item): bool {
            $content = $item->getAttribute('resolved_content');

            return $content !== null
                && (bool) $content->isActive
                && (string) $content->status === 'published';
        })->values();
        if ($validContents->isEmpty()) {
            throw new RuntimeException('Newsletter campaign has no valid published content.');
        }

        return $campaign->setRelation('contents', $validContents);
    }

    /**
     * @param  array{api_key: string, base_url: string, list_id: int, sender_email: string, sender_name: string}  $configuration
     */
    private function createCampaign(NewsletterCampaign $campaign, array $configuration): int
    {
        $html = view('frontend.mails.newsletter-campaign', ['campaign' => $campaign, 'isTest' => false])->render();
        if (! str_contains($html, '{{ contact.'.NewsletterBrevoService::UNSUBSCRIBE_ATTRIBUTE.' }}')) {
            throw new RuntimeException('Newsletter unsubscribe personalization placeholder is missing.');
        }

        $response = $this->client($configuration)->post('/emailCampaigns', [
            'name' => $campaign->title.' #'.$campaign->getKey(),
            'subject' => $campaign->title,
            'sender' => ['email' => $configuration['sender_email'], 'name' => $configuration['sender_name']],
            'recipients' => ['listIds' => [$configuration['list_id']]],
            'htmlContent' => $html,
        ])->throw();

        $campaignId = filter_var($response->json('id'), FILTER_VALIDATE_INT);
        if ($campaignId === false || $campaignId < 1) {
            throw new RuntimeException('Brevo did not return a valid campaign identifier.');
        }

        return (int) $campaignId;
    }

    /**
     * @param  array{api_key: string, base_url: string, list_id: int, sender_email: string, sender_name: string}  $configuration
     */
    private function sendCampaign(int $campaignId, array $configuration): void
    {
        $this->client($configuration)->post("/emailCampaigns/{$campaignId}/sendNow")->throw();
    }

    /**
     * @param  array{api_key: string, base_url: string, list_id: int, sender_email: string, sender_name: string}  $configuration
     */
    private function client(array $configuration): PendingRequest
    {
        return Http::baseUrl($configuration['base_url'])
            ->acceptJson()
            ->withHeaders(['api-key' => $configuration['api_key']])
            ->connectTimeout(5)
            ->timeout(20);
    }

    private function safeError(Throwable $exception, string $apiKey): string
    {
        $message = match (true) {
            $exception instanceof ConnectionException => 'Brevo connection failed.',
            $exception instanceof RequestException => $exception->response->json('message') ?: 'Brevo campaign delivery failed.',
            default => $exception->getMessage() ?: 'Brevo campaign delivery failed.',
        };

        $message = is_string($message) ? $message : 'Brevo campaign delivery failed.';
        if ($apiKey !== '') {
            $message = str_replace($apiKey, '[redacted]', $message);
        }

        return Str::limit($message, 1000, '');
    }
}
