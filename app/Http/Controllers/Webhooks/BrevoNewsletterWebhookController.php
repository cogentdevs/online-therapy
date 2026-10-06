<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrevoNewsletterWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->authorized($request)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $event = mb_strtolower(trim((string) $request->input('event')));
        $type = mb_strtolower(trim((string) $request->input('type', 'marketing')));
        $email = mb_strtolower(trim((string) $request->input('email')));
        $listIds = array_map('intval', (array) $request->input('list_id', []));
        $newsletterListId = filter_var(config('services.brevo_newsletter.list_id'), FILTER_VALIDATE_INT);

        if (! in_array($event, ['unsubscribe', 'unsubscribed'], true)
            || $type !== 'marketing'
            || $email === ''
            || $newsletterListId === false
            || ! in_array((int) $newsletterListId, $listIds, true)) {
            return response()->json(['success' => true, 'processed' => false]);
        }

        $subscriber = NewsletterSubscriber::query()->where('email', $email)->first();

        if ($subscriber === null) {
            return response()->json(['success' => true, 'processed' => false]);
        }

        if ($subscriber->status !== NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
            $subscriber->forceFill([
                'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
                'unsubscribed_at' => now(),
                'unsubscribe_reason' => 'Brevo newsletter unsubscribe',
                'brevo_sync_status' => NewsletterSubscriber::SYNC_SYNCED,
                'brevo_synced_at' => now(),
                'brevo_error' => null,
            ])->save();
        }

        return response()->json(['success' => true, 'processed' => true]);
    }

    private function authorized(Request $request): bool
    {
        $configuredToken = (string) config('services.brevo_newsletter.webhook_token');
        $providedToken = (string) $request->bearerToken();

        return $configuredToken !== '' && $providedToken !== '' && hash_equals($configuredToken, $providedToken);
    }
}
