<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferenceEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 10;

    public function __construct(
        public string $sender,
        public float $amount
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transferência realizada com sucesso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transference',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }
}
