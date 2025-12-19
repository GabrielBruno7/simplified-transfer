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

    public function loadByUser(Wallet $wallet): bool
    {
        $result = DB::table('wallets')
            ->select([
                'id',
                'balance',
            ])
            ->where('user_id', $wallet->getUser()->getId())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $wallet
            ->setId($result->id)
            ->setBalance($result->balance)
        ;

        return true;
    }

    public function updateBalance(Wallet $wallet): Wallet
    {
        DB::table('wallets')
            ->where('id', $wallet->getId())
            ->update([
                'balance' => $wallet->getBalance(),
                'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ])
        ;

        return $wallet;
    }
}
