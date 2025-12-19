<?php

namespace Domain\Wallet;

interface WalletPersistenceInterface
{
    public function create(Wallet $wallet): Wallet;
    public function loadByUser(Wallet $wallet): bool;
    public function updateBalance(Wallet $wallet): Wallet;
}
