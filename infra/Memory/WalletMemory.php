<?php

namespace Infra\Memory;

use Domain\Wallet\Wallet;
use Domain\Wallet\WalletPersistenceInterface;

class WalletMemory implements WalletPersistenceInterface
{
    public function create(Wallet $wallet): Wallet
    {
        return $wallet;
    }

    public function loadByUser(Wallet $wallet): bool
    {
        return false;
    }

    public function updateBalance(Wallet $wallet): Wallet
    {
        return $wallet;
    }
}
