<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterCampaign $campaign) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[TEST] '.$this->campaign->title);
    }

    public function content(): Content
    {
        return new Content(view: 'frontend.mails.newsletter-campaign', with: ['isTest' => true]);
    }
}
