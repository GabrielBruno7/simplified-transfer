<?php

namespace Infra\Database;

use Domain\Transfer\Transfer;
use Domain\Transfer\TransferPersistenceInterface;
use Illuminate\Support\Facades\DB;


class TransferDb implements TransferPersistenceInterface
{
    public function registerTransfer(Transfer $transfer): Transfer
    {
        DB::table('transfers')->insert([
            'id' => $transfer->getId(),
            'from_wallet_id' => $transfer->getPayer()->getWallet()->getId(),
            'to_wallet_id' => $transfer->getPayee()->getWallet()->getId(),
            'amount' => $transfer->getValue(),
        ]);

        return $transfer;
    }
}
