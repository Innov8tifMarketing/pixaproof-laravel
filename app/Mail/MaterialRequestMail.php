<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells marketing someone downloaded a material. Replying answers the visitor.
 */
class MaterialRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string>  $details  the submitted values by label
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $materialTitle,
        public array $details,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "PixaProof download: {$this->materialTitle}, by {$this->name}",
            replyTo: [$this->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.material-request');
    }
}
