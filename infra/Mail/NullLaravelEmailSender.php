<?php

namespace Infra\Mail;

use Domain\Transfer\Transfer;
use Domain\Notification\EmailSenderInterface;

class NullLaravelEmailSender implements EmailSenderInterface
{
    public function sendTransferCompleted(Transfer $transfer): void
    {
    }
}
