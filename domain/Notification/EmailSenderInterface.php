<?php

namespace Domain\Notification;

use Domain\Transfer\Transfer;

interface EmailSenderInterface
{
    public function sendTransferCompleted(Transfer $transfer): void;
}
