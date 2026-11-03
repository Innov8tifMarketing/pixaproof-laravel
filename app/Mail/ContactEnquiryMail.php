<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the team about a contact form enquiry. Replying answers the visitor.
 */
class ContactEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string>  $details  the submitted values by label
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $company,
        public array $details,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New PixaProof enquiry: {$this->name}, {$this->company}",
            replyTo: [$this->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-enquiry');
    }
}
