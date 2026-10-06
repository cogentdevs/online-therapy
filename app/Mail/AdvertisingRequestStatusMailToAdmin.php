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

class AdvertisingRequestStatusMailToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdRequest $adRequest,
        public string $status,
        public ?AdRequestPlacement $placement = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@digitalmagazine.com', 'Digital Magazine'),
            subject: 'تشہیری درخواست کی تازہ صورتحال — '.$this->adRequest->request_no
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'frontend.mails.advertising-request-status-mail-to-admin',
            with: ['statusLabel' => AdvertisingRequestStatusMailToUser::labelFor($this->status)],
        );
    }
}
