<?php

namespace Infra\Mail;

use Domain\Transfer\Transfer;
use App\Mail\TransferenceEmail;
use Illuminate\Support\Facades\Mail;
use Domain\Notification\EmailSenderInterface;

class LaravelEmailSender implements EmailSenderInterface
{
    public function sendTransferCompleted(Transfer $transfer): void
    {
        Mail::to(env('DEFAULT_EMAIL_RECEIVER'))
            ->queue(new TransferenceEmail(
                sender: $transfer->getPayer()->getName(),
                amount: $transfer->getValue()
            ))
        ;
    }
}
