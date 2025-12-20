<?php

namespace Tests\Unit;

use Domain\ErrorCodes;
use Domain\User\User;
use Domain\Wallet\Wallet;
use Infra\Memory\UserMemory;
use Infra\Memory\WalletMemory;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUserCommonWithValidData(): void
    {
        $wallet = (new Wallet(new WalletMemory()));

        $user = (new User(new UserMemory()))
            ->setWallet($wallet)
            ->setDocument('12345678903')
            ->setPassword('securepassword')
            ->setType(User::USER_TYPE_COMMON)
            ->setEmail('ana.pereira@example.com')
            ->create()
        ;

        $this->assertEquals(User::USER_TYPE_COMMON, $user->getType());
    }

    public function testCreateUserMerchantWithValidData(): void
    {
        $wallet = (new Wallet(new WalletMemory()));

        $user = (new User(new UserMemory()))
            ->setWallet($wallet)
            ->setDocument('12345678902')
            ->setPassword('securepassword')
            ->setType(User::USER_TYPE_MERCHANT)
            ->setEmail('ana.pereira@example.com')
            ->create()
        ;

        $this->assertEquals(User::USER_TYPE_MERCHANT, $user->getType());
    }

    public function testCreateUserWithInvalidTypeThrowsException(): void
    {
        $invalidType = 'invalid_type';

        $this->expectExceptionCode(ErrorCodes::USER_USER_INVALID_TYPE);
        $this->expectExceptionMessage("The type '{$invalidType}' is invalid");

        (new User(new UserMemory()))->setType($invalidType);
    }

    public function testCreateUserThatAlreadyExistsThrowsException(): void
    {
        $wallet = (new Wallet(new WalletMemory()));

        $user = (new User(new UserMemory()))
            ->setWallet($wallet)
            ->setDocument('12345678901')
            ->setPassword('securepassword')
            ->setType(User::USER_TYPE_MERCHANT)
            ->setEmail('ana.pereira@example.com')
        ;

        $this->expectExceptionMessage('The user already exists');
        $this->expectExceptionCode(ErrorCodes::USER_ERROR_ALREADY_EXISTS);

        $user->create();
    }
}
