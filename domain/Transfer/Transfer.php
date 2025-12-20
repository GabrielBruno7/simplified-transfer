<?php

namespace Domain\Transfer;

use Domain\User\User;
use Domain\ErrorCodes;
use Domain\Helper\Helper;
use Domain\UserException;
use Illuminate\Support\Facades\DB;
use Domain\Notification\EmailSenderInterface;


class Transfer
{
    private string $id;
    private User $payee;
    private User $payer;
    private float $value;
    private string $createdAt;
    private EmailSenderInterface $emailSender;
    private TransferAuthorizerInterface $authorizer;
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

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setValue(float $value): self
    {
        if ($value <= 0) {
            throw new UserException(
                ErrorCodes::USER_ERROR_TRANSFER_VALUE_MUST_BE_GREATER_THAN_ZERO,
                "The transfer value '{$value}' must be greater than zero"
            );
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

    public function setAuthorizer(TransferAuthorizerInterface $authorizer): self
    {
        $this->authorizer = $authorizer;

        return $this;
    }

    public function getAuthorizer(): TransferAuthorizerInterface
    {
        return $this->authorizer;
    }

    public function setEmailSender(EmailSenderInterface $emailSender): self
    {
        $this->emailSender = $emailSender;

        return $this;
    }

    public function getEmailSender(): EmailSenderInterface
    {
        return $this->emailSender;
    }

    public function execute(): Transfer
    {
        $this->checkIfTransferenceIsForSameUser();
        $this->checkIfTransferByMerchant();
        $this->checkPayerWalletBalance();

        $this->checkIfTransferenceIsAuthorized();

        DB::transaction(function () {
            $this->updatePayerBalance();
            $this->updatePayeeBalance();
            $this->registerTransfer();
        });

        $this->sendNotifications();

        return $this;
    }

    private function sendNotifications(): void
    {
        $this->getEmailSender()->sendTransferCompleted($this);
    }

    private function checkIfTransferenceIsAuthorized(): void
    {
        if (!$this->getAuthorizer()->authorize()) {
            throw new UserException(
                ErrorCodes::USER_ERROR_TRANSFER_NOT_AUTHORIZED,
                "The transfer not authorized by external service"
            );
        }
    }

    private function registerTransfer(): void
    {
        $this->setId(Helper::generateUuid());

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
            throw new UserException(
                ErrorCodes::USER_ERROR_INSUFFICIENT_FUNDS,
                "The balance '{$this->getPayer()->getWallet()->getBalance()}' payer has insufficient funds to make the transfer"
            );
        }
    }

    private function checkIfTransferByMerchant(): void
    {
        if ($this->getPayer()->getType() === User::USER_TYPE_MERCHANT) {
            throw new UserException(
                ErrorCodes::USER_ERROR_MERCHANT_CANNOT_TRANSFER,
                'Merchant users cannot make transfers'
            );
        }
    }

    private function checkIfTransferenceIsForSameUser(): void
    {
        if ($this->getPayee()->getId() === $this->getPayer()->getId()) {
            throw new UserException(
                ErrorCodes::USER_ERROR_SAME_USER_TRANSFER,
                'Payer and payee cannot be the same user',
            );
        }
    }
}
