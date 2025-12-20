<?php

namespace Infra\Database;

use Domain\Transfer\Transfer;
use Illuminate\Support\Facades\DB;
use Domain\Transfer\TransferPersistenceInterface;

class TransferDb implements TransferPersistenceInterface
{
    public function registerTransfer(Transfer $transfer): Transfer
    {
        DB::table('transfers')->insert([
            'id' => $transfer->getId(),
            'amount' => $transfer->getValue(),
            'status' => $transfer->getStatus(),
            'created_at' => $transfer->getCreatedAt(),
            'to_wallet_id' => $transfer->getPayee()->getWallet()->getId(),
            'from_wallet_id' => $transfer->getPayer()->getWallet()->getId(),
        ]);

        return $transfer;
    }
}
