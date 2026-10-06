<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorOtpMailToUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public string $otp, public string $purpose = 'enable') {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@digitalmagazine.com', 'Digital Magazine'),
            subject: match ($this->purpose) {
                'login' => 'لاگ ان تصدیقی کوڈ',
                'disable' => 'دو مرحلہ توثیق غیر فعال کرنے کا کوڈ',
                'change_method' => 'نئے دو مرحلہ توثیقی طریقے کا کوڈ',
                default => 'دو مرحلہ توثیق کا تصدیقی کوڈ',
            },
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'frontend.mails.two-factor-otp-mail-to-user',
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
}
