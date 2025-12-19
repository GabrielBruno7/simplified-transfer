<?php

namespace Domain\User;

use Domain\Helper\Helper;

class User
{
    private string $id;
    private string $name;
    private string $type;
    private string $email;
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

    public function setType(string $type): self
    {
        if (!in_array($type, self::ALLOWED_USER_TYPES, true)) {
            throw new \InvalidArgumentException('Invalid user type');
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

        $this->setId(Helper::generateUuid());

        $this->setPassword(bcrypt($this->getPassword()));

        $this->persistence->create($this);

        return $this;
    }

    private function checkIfUserAlreadyExists(): self
    {
        if ($this->loadUserByEmailOrDocument()) {
            throw new \RuntimeException(
                'User with given email or document already exists' //TODO: Adjust message
            );
        }

        return $this;
    }

    public function loadUserByEmailOrDocument(): bool
    {
        return $this->persistence->findUserByEmailOrDocument($this);
    }
}
