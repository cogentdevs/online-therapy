<?php

namespace App\Mail;

use App\Models\AskQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AskQuestionResponseMailToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AskQuestion $askQuestion) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@digitalmagazine.com', 'Digital Magazine'),
            subject: 'سوال کا جواب اپ ڈیٹ ہوا — '.$this->askQuestion->question_no,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'frontend.mails.ask-question-response-mail-to-admin',
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
