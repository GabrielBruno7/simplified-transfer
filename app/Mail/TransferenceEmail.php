<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

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

    public function sendWithFailureSimulation()
    {
        if (random_int(0, 1) === 1) {
            throw new \RuntimeException('Falha no envio de email');
        }

        Mail::to(env('DEFAULT_EMAIL_RECEIVER'))->send($this);
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }
}
