<?php

namespace Domain\User;

use Domain\Helper\Helper;

class User
{
    private string $id;
    private string $name;
    private string $email;
    private string $password;
    private string $document;
    private ?string $type = null;
    private UserPersistenceInterface $persistence;

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

    public function setType(?string $type): self
    {
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
        $this->setId(Helper::generateUuid());

        $this->persistence->create($this);

        return $this;
    }
}
