<?php

namespace Domain\Transfer;

use Domain\Helper\Helper;
use Domain\User\User;
use Illuminate\Support\Facades\DB;

class Transfer
{
    private string $id;
    private User $payee;
    private User $payer;
    private float $value;
    private TransferPersistenceInterface $persistence;

    public function __construct(TransferPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): TransferPersistenceInterface
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

    public function setValue(float $value): self
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Value must be greater than zero'); //TODO: Custom Exception
        }

        $this->value = $value;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setPayee(User $payee): self
    {
        $this->payee = $payee;

        return $this;
    }

    public function getPayee(): User
    {
        return $this->payee;
    }

    public function setPayer(User $payer): self
    {
        $this->payer = $payer;

        return $this;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function execute(): Transfer
    {
        $this->checkIfTransferenceIsForSameUser();
        $this->checkIfTransferByMerchant();
        $this->checkPayerWalletBalance();

        return DB::transaction(function () {
            $this->setId(Helper::generateUuid());

            $this->updatePayerBalance();
            $this->updatePayeeBalance();
            $this->registerTransfer();

            return $this;
        });
    }

    private function registerTransfer(): void
    {
        $this->getPersistence()->registerTransfer($this);
    }

    private function updatePayerBalance(): void
    {
        $payerWallet = $this->getPayer()->getWallet();

        $payerNewBalance = round($payerWallet->getBalance() - $this->getValue(), 2);

        $payerWallet->setBalance($payerNewBalance);

        $payerWallet->updateBalance();
    }

    private function updatePayeeBalance(): void
    {
        $payeeWallet = $this->getPayee()->getWallet();

        $payeeNewBalance = round($payeeWallet->getBalance() + $this->getValue(), 2);

        $payeeWallet->setBalance($payeeNewBalance);

        $payeeWallet->updateBalance();
    }

    private function checkPayerWalletBalance(): void
    {
        if ($this->getPayer()->getWallet()->getBalance() < $this->getValue()) {
            throw new \InvalidArgumentException('Insufficient balance for the transfer'); //TODO: Custom Exception
        }
    }

    private function checkIfTransferByMerchant(): void
    {
        if ($this->getPayer()->getType() === User::USER_TYPE_MERCHANT) {
            throw new \InvalidArgumentException('Merchants are not allowed to make transfers'); //TODO: Custom Exception
        }
    }

    private function checkIfTransferenceIsForSameUser(): void
    {
        if ($this->getPayee()->getId() === $this->getPayer()->getId()) {
            throw new \InvalidArgumentException('Payer and payee cannot be the same user'); //TODO: Custom Exception
        }
    }
}
