<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\UnsubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterBrevoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NewsletterUnsubscribeController extends Controller
{
    /** @var array<string, string> */
    private const REASONS = [
        'too_many' => 'مجھے بہت زیادہ ای میلز موصول ہو رہی ہیں',
        'not_relevant' => 'مواد میرے لیے متعلقہ نہیں ہے',
        'not_now' => 'میں فی الحال نیوز لیٹر وصول نہیں کرنا چاہتا',
        'other' => 'دیگر',
    ];

    public function show(string $token): View
    {
        $subscriber = $this->subscriber($token);

        return view('frontend.newsletter-unsubscribe', [
            'subscriber' => $subscriber,
            'reasons' => self::REASONS,
        ]);
    }

    public function store(UnsubscribeNewsletterRequest $request, string $token, NewsletterBrevoService $brevo): RedirectResponse
    {
        $subscriber = $this->subscriber($token);

        if ($subscriber->status !== NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
            $reason = $request->validated('reason') === 'other'
                ? trim((string) $request->validated('other_reason'))
                : self::REASONS[$request->validated('reason')];

            $subscriber->forceFill([
                'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
                'unsubscribed_at' => now(),
                'unsubscribe_reason' => $reason,
                'brevo_sync_status' => NewsletterSubscriber::SYNC_PENDING,
                'brevo_error' => null,
            ])->save();

            $brevo->sync($subscriber);
        } elseif ($subscriber->brevo_sync_status === NewsletterSubscriber::SYNC_FAILED) {
            $brevo->sync($subscriber);
        }

        return to_route('front.newsletter.unsubscribe.success');
    }

    public function success(): View
    {
        return view('frontend.newsletter-unsubscribe-success');
    }

    private function subscriber(string $token): NewsletterSubscriber
    {
        return NewsletterSubscriber::query()->where('unsubscribe_token', $token)->firstOrFail();
    }
}
