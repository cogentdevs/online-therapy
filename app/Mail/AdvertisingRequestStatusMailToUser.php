<?php

namespace App\Mail;

use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdvertisingRequestStatusMailToUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdRequest $adRequest,
        public string $status,
        public ?AdRequestPlacement $placement = null,
    ) {}

    public function statusLabel(): string
    {
        return self::labelFor($this->status);
    }

    public static function labelFor(string $status): string
    {
        return match ($status) {
            'quote_sent' => 'پیشکش بھیج دی گئی',
            'confirmed' => 'تشہیری مقام کی تصدیق ہو گئی',
            'published' => 'تشہیر شائع ہو گئی',
            'rejected' => 'درخواست مسترد کر دی گئی',
            'cancelled' => 'درخواست منسوخ کر دی گئی',
            default => 'درخواست کی صورتحال تبدیل ہوئی',
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@digitalmagazine.com', 'Digital Magazine'),
            subject: $this->statusLabel().' — '.$this->adRequest->request_no
        );
    }

    public function content(): Content
    {
        return new Content(view: 'frontend.mails.advertising-request-status-mail-to-user', with: ['statusLabel' => $this->statusLabel()]);
    }
}
