<?php

namespace App\Mail;

use App\Models\SubscriptionType;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SubscriptionExpiredMailToUser extends Mailable
{
    use Queueable, SerializesModels;

    public const MAILER = 'brevo';

    public function __construct(public UserSubscription $userSubscription) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $senderAddress = config('mail.brevo_from.address');

        return new Envelope(
            // from: filled($senderAddress)
            //     ? new Address((string) $senderAddress, (string) config('mail.brevo_from.name', config('app.name')))
            //     : null,
            from: new Address('cogentdevs@gmail.com', 'Digital Magazine'),
            subject: 'آپ کی سبسکرپشن کی میعاد ختم ہو گئی ہے',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'frontend.mails.subscription-expired-mail-to-user',
            with: [
                'endDate' => $this->userSubscription->end_date?->format('Y-m-d') ?? 'درج نہیں',
                'moduleLabels' => $this->moduleLabels(),
                'productName' => filled($this->userSubscription->product_name)
                    ? $this->userSubscription->product_name
                    : 'سبسکرپشن',
                'productType' => $this->productTypeLabel(),
                'recipientName' => filled($this->userSubscription->user?->name)
                    ? $this->userSubscription->user->name
                    : 'محترم صارف',
                'resubscribeUrl' => route('front.subscriptions'),
                'startDate' => $this->userSubscription->start_date?->format('Y-m-d') ?? 'درج نہیں',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /** @return list<string> */
    private function moduleLabels(): array
    {
        return $this->userSubscription->subscriptionTypes
            ->map(fn (SubscriptionType $type): string => match (Str::lower(trim((string) $type->name))) {
                'magazine', 'magazines' => 'ہفتہ وار میگزین',
                'article', 'articles' => 'مضامین',
                'audio' => 'آڈیو',
                default => (string) $type->name,
            })
            ->values()
            ->all();
    }

    private function productTypeLabel(): string
    {
        return match (Str::lower(trim((string) $this->userSubscription->product_for))) {
            'plan' => 'منصوبہ',
            'membership' => 'رکنیت',
            default => filled($this->userSubscription->product_for)
                ? (string) $this->userSubscription->product_for
                : 'سبسکرپشن',
        };
    }
}
