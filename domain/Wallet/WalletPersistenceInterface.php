<?php

namespace Domain\Wallet;

interface WalletPersistenceInterface
{
    public function create(Wallet $wallet): Wallet;
}
