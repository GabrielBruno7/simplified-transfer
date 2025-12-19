<?php

namespace Domain\Transfer;

use Domain\Transfer\Transfer;

interface TransferPersistenceInterface
{
    public function registerTransfer(Transfer $transfer): Transfer;
}
