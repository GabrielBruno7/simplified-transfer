<?php

namespace Infra\Database;

use Domain\Wallet\Wallet;
use Illuminate\Support\Facades\DB;
use Domain\Wallet\WalletPersistenceInterface;

class WalletDb implements WalletPersistenceInterface
{
    public function create(Wallet $wallet): Wallet
    {
        DB::table('wallets')->insert([
            'id' => $wallet->getId(),
            'balance' => $wallet->getBalance(),
            'user_id' => $wallet->getUser()->getId(),
        ]);

        return $wallet;
    }
}
