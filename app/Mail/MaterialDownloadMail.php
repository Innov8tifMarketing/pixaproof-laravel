<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sends a visitor the material they asked for, as a link that works for a week.
 */
class MaterialDownloadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $materialTitle,
        public string $downloadUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your PixaProof download: {$this->materialTitle}",
            replyTo: array_filter([config('services.lead_mail.marketing_to')]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.material-download');
    }
}
