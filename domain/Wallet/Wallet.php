<?php

namespace Domain\Wallet;

use Domain\User\User;
use Domain\ErrorCodes;
use Domain\UserException;

class Wallet
{
    private string $id;
    private User $user;
    private float $balance;
    private WalletPersistenceInterface $persistence;

    public function __construct(WalletPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): WalletPersistenceInterface
    {
        return $this->persistence;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setBalance(float $balance): self
    {
        if ($balance < 0) {
            throw new UserException(
                ErrorCodes::USER_ERROR_WALLET_BALANCE_CANNOT_BE_NEGATIVE,
                "The balance '{$balance}' cannot be negative"
            );
        }

        $this->balance = $balance;

        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function create(): Wallet
    {
        $this->persistence->create($this);

        return $this;
    }

    public function loadByUser(): Wallet
    {
        if (!$this->getPersistence()->loadByUser($this)) {
            throw new UserException(
                ErrorCodes::USER_ERROR_WALLET_NOT_FOUND,
                "The wallet for user '{$this->getUser()->getId()}' was not found"
            );
        }

        return $this;
    }

    public function updateBalance(): Wallet
    {
        $this->getPersistence()->updateBalance($this);

        return $this;
    }
}
