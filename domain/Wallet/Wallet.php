<?php

namespace Domain\Wallet;

use Domain\User\User;

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
            throw new \InvalidArgumentException('Balance cannot be negative'); //TODO: Custom Exception
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
}
