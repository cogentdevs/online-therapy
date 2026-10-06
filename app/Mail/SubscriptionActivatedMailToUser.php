<?php

namespace App\Mail;

use App\Models\UserSubscription;
use App\Services\SubscriptionInvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivatedMailToUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public UserSubscription $userSubscription) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $isFutureSubscription = $this->userSubscription->start_date?->isAfter(today()) === true;

        return new Envelope(
            from: new Address('cogentdevs@gmail.com', 'Digital Magazine'),
            subject: $isFutureSubscription
                ? 'آپ کی سبسکرپشن کی تجدید کامیابی سے طے ہو گئی ہے'
                : 'آپ کی سبسکرپشن کامیابی سے فعال ہو گئی ہے',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'frontend.mails.subscription-activated-mail-to-user',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $invoiceService = app(SubscriptionInvoiceService::class);

        return [
            Attachment::fromData(
                fn (): string => $invoiceService->content($this->userSubscription),
                $invoiceService->filename($this->userSubscription),
            )->withMime('application/pdf'),
        ];
    }
}
