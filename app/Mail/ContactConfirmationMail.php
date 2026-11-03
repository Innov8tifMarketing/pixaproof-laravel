<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells a visitor their contact form message arrived. Replying reaches sales.
 */
class ContactConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $name) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your message',
            replyTo: array_filter([config('services.lead_mail.sales_cc')]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-confirmation');
    }
}
