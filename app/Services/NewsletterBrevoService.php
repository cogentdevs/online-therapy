<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class NewsletterBrevoService
{
    public const UNSUBSCRIBE_ATTRIBUTE = 'DM_UNSUB_TOKEN';

    private bool $unsubscribeAttributeReady = false;

    public function sync(NewsletterSubscriber $subscriber): bool
    {
        $subscriber->forceFill([
            'brevo_sync_status' => NewsletterSubscriber::SYNC_PENDING,
            'brevo_error' => null,
        ])->save();

        try {
            $configuration = $this->configuration();

            if ($subscriber->status === NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
                $this->removeFromNewsletterList($subscriber, $configuration);
            } else {
                $token = $subscriber->ensureUnsubscribeToken();
                $this->ensureUnsubscribeAttribute($configuration);
                $this->addToNewsletterList($subscriber, $token, $configuration);
            }

            $subscriber->forceFill([
                'brevo_sync_status' => NewsletterSubscriber::SYNC_SYNCED,
                'brevo_synced_at' => now(),
                'brevo_error' => null,
            ])->save();

            return true;
        } catch (Throwable $exception) {
            report($exception);

            $subscriber->forceFill([
                'brevo_sync_status' => NewsletterSubscriber::SYNC_FAILED,
                'brevo_error' => $this->safeError($exception),
            ])->save();

            return false;
        }
    }

    public function assertCampaignPersonalizationReady(): void
    {
        $configuration = $this->configuration();
        $this->ensureUnsubscribeAttribute($configuration);

        $incompleteSubscribers = NewsletterSubscriber::query()
            ->where('status', NewsletterSubscriber::STATUS_SUBSCRIBED)
            ->where(function ($query): void {
                $query->whereNull('unsubscribe_token')
                    ->orWhere('unsubscribe_token', '')
                    ->orWhere('brevo_sync_status', '!=', NewsletterSubscriber::SYNC_SYNCED);
            })
            ->count();

        if ($incompleteSubscribers > 0) {
            throw new RuntimeException("{$incompleteSubscribers} subscribed Newsletter contact(s) require token synchronization. Run newsletter:sync-unsubscribe-tokens before sending.");
        }
    }

    /** @return array{api_key: string, base_url: string, list_id: int} */
    private function configuration(): array
    {
        $apiKey = trim((string) config('services.brevo_newsletter.api_key'));
        $baseUrl = rtrim((string) config('services.brevo_newsletter.base_url', 'https://api.brevo.com/v3'), '/');
        $listId = filter_var(config('services.brevo_newsletter.list_id'), FILTER_VALIDATE_INT);

        if ($apiKey === '' || $baseUrl === '' || $listId === false || $listId < 1) {
            throw new RuntimeException('Newsletter Brevo configuration is incomplete.');
        }

        return ['api_key' => $apiKey, 'base_url' => $baseUrl, 'list_id' => (int) $listId];
    }

    /** @param array{api_key: string, base_url: string, list_id: int} $configuration */
    private function ensureUnsubscribeAttribute(array $configuration): void
    {
        if ($this->unsubscribeAttributeReady) {
            return;
        }

        if ($this->attributeExists($configuration)) {
            $this->unsubscribeAttributeReady = true;

            return;
        }

        try {
            $this->client($configuration)
                ->post('/contacts/attributes/normal/'.self::UNSUBSCRIBE_ATTRIBUTE, ['type' => 'text'])
                ->throw();
            $this->unsubscribeAttributeReady = true;
        } catch (RequestException $exception) {
            if (! $this->attributeExists($configuration)) {
                throw $exception;
            }

            $this->unsubscribeAttributeReady = true;
        }
    }

    /** @param array{api_key: string, base_url: string, list_id: int} $configuration */
    private function attributeExists(array $configuration): bool
    {
        $attributes = $this->client($configuration)->get('/contacts/attributes')->throw()->json('attributes', []);

        foreach ($attributes as $attribute) {
            if (($attribute['name'] ?? null) === self::UNSUBSCRIBE_ATTRIBUTE) {
                if (($attribute['category'] ?? null) !== 'normal' || ($attribute['type'] ?? null) !== 'text') {
                    throw new RuntimeException(self::UNSUBSCRIBE_ATTRIBUTE.' exists in Brevo with an incompatible type.');
                }

                return true;
            }
        }

        return false;
    }

    /** @param array{api_key: string, base_url: string, list_id: int} $configuration */
    private function addToNewsletterList(NewsletterSubscriber $subscriber, string $token, array $configuration): void
    {
        $this->client($configuration)->post('/contacts', [
            'email' => $subscriber->email,
            'attributes' => [self::UNSUBSCRIBE_ATTRIBUTE => $token],
            'listIds' => [$configuration['list_id']],
            'updateEnabled' => true,
        ])->throw();
    }

    /** @param array{api_key: string, base_url: string, list_id: int} $configuration */
    private function removeFromNewsletterList(NewsletterSubscriber $subscriber, array $configuration): void
    {
        $response = $this->client($configuration)
            ->put('/contacts/'.rawurlencode($subscriber->email), [
                'unlinkListIds' => [$configuration['list_id']],
            ]);

        if (! $response->notFound()) {
            $response->throw();
        }
    }

    /** @param array{api_key: string, base_url: string, list_id: int} $configuration */
    private function client(array $configuration): PendingRequest
    {
        return Http::baseUrl($configuration['base_url'])
            ->acceptJson()
            ->withHeaders(['api-key' => $configuration['api_key']])
            ->connectTimeout(5)
            ->timeout(15);
    }

    private function safeError(Throwable $exception): string
    {
        if ($exception instanceof ConnectionException) {
            return 'Brevo connection failed.';
        }

        if ($exception instanceof RequestException) {
            $message = $exception->response->json('message');

            return Str::limit(is_string($message) && $message !== '' ? $message : 'Brevo synchronization failed.', 1000, '');
        }

        return Str::limit($exception->getMessage() !== '' ? $exception->getMessage() : 'Brevo synchronization failed.', 1000, '');
    }
}
