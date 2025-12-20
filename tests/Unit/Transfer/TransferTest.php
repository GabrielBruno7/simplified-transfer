<?php

namespace Tests\Unit;

use Domain\User\User;
use Domain\ErrorCodes;
use Domain\UserException;
use Domain\Wallet\Wallet;
use Infra\Memory\UserMemory;
use Domain\Transfer\Transfer;
use Infra\Memory\WalletMemory;
use PHPUnit\Framework\TestCase;
use Infra\Memory\TransferMemory;
use Infra\Mail\NullLaravelEmailSender;
use Infra\Authorizer\NullTransferAuthorizerTrue;
use Infra\Authorizer\NullTransferAuthorizerFalse;

class TransferTest extends TestCase
{
    public function testSetValueWithZeroThrowsException(): void
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage("The transfer value '0' must be greater than zero");

        (new Transfer(new TransferMemory()))->setValue(0);
    }

    public function testSetValueWithNegativeAmountThrowsException(): void
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage("The transfer value '-50' must be greater than zero");

        (new Transfer(new TransferMemory()))->setValue(-50.0);
    }

    public function testTransferBetweenSameUserThrowsException(): void
    {
        $userMemory = new UserMemory();
        $walletMemory = new WalletMemory();

        $wallet = (new Wallet($walletMemory))->setBalance(100.0);

        $user = (new User($userMemory))
            ->setWallet($wallet)
            ->setName('Jorge Silva')
            ->setEmail('same@test.com')
            ->setDocument('13609120029')
            ->setType(User::USER_TYPE_COMMON)
            ->setId('b2541b69-a77d-4b44-9381-8284da175dbf')
        ;
        
        $this->expectException(UserException::class);
        $this->expectExceptionCode(ErrorCodes::USER_ERROR_SAME_USER_TRANSFER);

        (new Transfer(new TransferMemory()))
            ->setValue(50.0)
            ->setPayer($user)
            ->setPayee($user)
            ->setEmailSender(new NullLaravelEmailSender())
            ->setAuthorizer(new NullTransferAuthorizerTrue())
            ->execute()
        ;
    }

    public function testMerchantUserCannotTransfer(): void
    {
        $payerWallet = (new Wallet(new WalletMemory()))->setBalance(200.0);
        $payeeWallet = (new Wallet(new WalletMemory()))->setBalance(50.0);

        $payerMerchant = (new User(new UserMemory()))
            ->setName('Lucas Leal')
            ->setWallet($payerWallet)
            ->setDocument('12345678000195')
            ->setType(User::USER_TYPE_MERCHANT)
            ->setEmail('contato@lojacentral.com')
            ->setId('f9e7d70a-fad0-4d4a-83c8-33e5cfc4805f')
        ;

        $payeeCommon = (new User(new UserMemory()))
            ->setName('Ana Pereira')
            ->setWallet($payeeWallet)
            ->setDocument('98765432100')
            ->setType(User::USER_TYPE_COMMON)
            ->setEmail('ana.pereira@example.com')
            ->setId('3cc7036c-8254-4287-941e-eb01c5a2c9d5')
        ;
        
        $this->expectException(UserException::class);
        $this->expectExceptionCode(ErrorCodes::USER_ERROR_MERCHANT_CANNOT_TRANSFER);

        (new Transfer(new TransferMemory()))
            ->setValue(50.0)
            ->setPayee($payeeCommon)
            ->setPayer($payerMerchant)
            ->setEmailSender(new NullLaravelEmailSender())
            ->setAuthorizer(new NullTransferAuthorizerTrue())
            ->execute()
        ;
    }

    public function testInsufficientFundsThrowsException(): void
    {
        $payerWallet = (new Wallet(new WalletMemory()))->setBalance(30.0);
        $payeeWallet = (new Wallet(new WalletMemory()))->setBalance(50.0);

        $payerMerchant = (new User(new UserMemory()))
            ->setWallet($payerWallet)
            ->setName('Roberto Justus')
            ->setDocument('12345678000195')
            ->setType(User::USER_TYPE_COMMON)
            ->setEmail('contato@lojacentral.com')
            ->setId('f9e7d70a-fad0-4d4a-83c8-33e5cfc4805f')
        ;

        $payeeCommon = (new User(new UserMemory()))
            ->setName('Ana Pereira')
            ->setWallet($payeeWallet)
            ->setDocument('98765432100')
            ->setType(User::USER_TYPE_COMMON)
            ->setEmail('ana.pereira@example.com')
            ->setId('3cc7036c-8254-4287-941e-eb01c5a2c9d5')
        ;
        
        $this->expectException(UserException::class);
        $this->expectExceptionCode(ErrorCodes::USER_ERROR_INSUFFICIENT_FUNDS);

        (new Transfer(new TransferMemory()))
            ->setValue(150.0)
            ->setPayee($payeeCommon)
            ->setPayer($payerMerchant)
            ->setEmailSender(new NullLaravelEmailSender())
            ->setAuthorizer(new NullTransferAuthorizerTrue())
            ->execute()
        ;
    }

    public function testTransferNotAuthorizedThrowsException(): void
    {
        $payeeWallet = (new Wallet(new WalletMemory()))->setBalance(50.0);
        $payerWallet = (new Wallet(new WalletMemory()))->setBalance(150.00);

        $payerMerchant = (new User(new UserMemory()))
            ->setWallet($payerWallet)
            ->setName('Roberto Justus')
            ->setDocument('12345678000195')
            ->setType(User::USER_TYPE_COMMON)
            ->setEmail('contato@lojacentral.com')
            ->setId('f9e7d70a-fad0-4d4a-83c8-33e5cfc4805f')
        ;

        $payeeCommon = (new User(new UserMemory()))
            ->setName('Ana Pereira')
            ->setWallet($payeeWallet)
            ->setDocument('98765432100')
            ->setType(User::USER_TYPE_COMMON)
            ->setEmail('ana.pereira@example.com')
            ->setId('3cc7036c-8254-4287-941e-eb01c5a2c9d5')
        ;

        $this->expectException(UserException::class);
        $this->expectExceptionCode(ErrorCodes::USER_ERROR_TRANSFER_NOT_AUTHORIZED);

        (new Transfer(new TransferMemory()))
            ->setValue(150.0)
            ->setPayee($payeeCommon)
            ->setPayer($payerMerchant)
            ->setEmailSender(new NullLaravelEmailSender())
            ->setAuthorizer(new NullTransferAuthorizerFalse())
            ->execute()
        ;
    }

    public function testTransferInvalidStatusThrowsException(): void
    {   
        $invalidStatus = 'invalid_status';

        $this->expectException(UserException::class);
        $this->expectExceptionCode(ErrorCodes::USER_ERROR_TRANSFER_STATUS_INVALID);

        (new Transfer(new TransferMemory()))->setStatus($invalidStatus);
    }
}
