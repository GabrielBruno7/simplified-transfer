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

    public function loadStatements(Wallet $wallet): array
    {
        $walletId = $wallet->getId();

        return DB::table('transfers as t')
            ->select([
                't.id',
                't.amount',
                't.status',
                't.created_at',
                'ut.name as to_name',
                'uf.name as from_name',
            ])
            ->join('wallets as wf', 'wf.id', '=', 't.from_wallet_id')
            ->join('wallets as wt', 'wt.id', '=', 't.to_wallet_id')
            ->join('users as uf', 'uf.id', '=', 'wf.user_id')
            ->join('users as ut', 'ut.id', '=', 'wt.user_id')
            ->where('t.from_wallet_id', $walletId)
            ->orWhere('t.to_wallet_id', $walletId)
            ->orderByDesc('t.created_at')
            ->get()
            ->toArray()
        ;
    }
}
