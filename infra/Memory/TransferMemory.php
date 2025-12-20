<?php

namespace Infra\Memory;

use Domain\Transfer\Transfer;
use Domain\Transfer\TransferPersistenceInterface;

class TransferMemory implements TransferPersistenceInterface
{
    public function registerTransfer(Transfer $transfer): Transfer
    {
        return $transfer;
    }
}