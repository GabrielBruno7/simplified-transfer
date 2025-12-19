<?php

namespace Domain\Transfer\Events;

class TransferCompleted
{
    public function __construct(
        public string $toEmail,
        public string $fromName,
        public float $amount
    ) {}
}
