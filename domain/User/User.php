<?php

namespace Domain\User;

use Domain\ErrorCodes;
use Domain\Helper\Helper;
use Domain\UserException;
use Domain\Wallet\Wallet;

class User
{
    private string $id;
    private string $name;
    private string $type;
    private string $email;
    private Wallet $wallet;
    private string $password;
    private string $document;
    private UserPersistenceInterface $persistence;

    public const USER_TYPE_COMMON = 'comum';
    public const USER_TYPE_MERCHANT = 'lojista';

    private const ALLOWED_USER_TYPES = [
        self::USER_TYPE_COMMON,
        self::USER_TYPE_MERCHANT,
    ];

    public function __construct(UserPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): UserPersistenceInterface
    {
        return $this->persistence;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setDocument(string $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, self::ALLOWED_USER_TYPES, true)) {
            throw new UserException(
                ErrorCodes::USER_USER_INVALID_TYPE,
                "The type '{$type}' is invalid"
            );
        }

        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
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

    public function create(): User
    {
        $this->checkIfUserAlreadyExists();

        $this->createUser();

        $this->createUserWallet();

        return $this;
    }

    private function createUserWallet(): self
    {
        $this
            ->getWallet()
            ->setUser($this)
            ->setBalance(0.0)
            ->setId(Helper::generateUuid())
            ->create()
        ;

        return $this;
    }

    private function createUser(): self
    {
        $this->setId(Helper::generateUuid());

        $this->getPersistence()->create($this);

        return $this;
    }

    private function checkIfUserAlreadyExists(): self
    {
        if ($this->loadUserByEmailOrDocument()) {
            throw new UserException(
                ErrorCodes::USER_ERROR_ALREADY_EXISTS,
                "The user already exists"
            );
        }

        return $this;
    }

    public function loadUserByEmailOrDocument(): bool
    {
        return $this->getPersistence()->findUserByEmailOrDocument($this);
    }

    public function loadUserByEmail(): self
    {
        if (!$this->getPersistence()->findUserByEmail($this)) {
            throw new UserException(
                ErrorCodes::USER_NOT_FOUND,
                "The user with email '{$this->getEmail()}' was not found"
            );
        }

        return $this;
    }

    public function loadByDocument(): User
    {
        if (!$this->getPersistence()->findUserByDocument($this)) {
            throw new UserException(
                ErrorCodes::USER_NOT_FOUND,
                "The user with document '{$this->getDocument()}' was not found"
            );
        }

        return $this;
    }
}
