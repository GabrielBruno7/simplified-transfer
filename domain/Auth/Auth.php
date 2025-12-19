<?php

namespace Domain\Auth;

use Domain\User\User;

class Auth
{
    private User $user;
    private string $token;
    private string $password;
    private AuthPersistenceInterface $persistence;

    public function __construct(AuthPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function authenticate(): self
    {
        $this->checkPassword();

        $this->generateToken();

        return $this;
    }

    private function checkPassword(): void
    {
        $isValidPassword = $this->persistence->verifyPassword(
            $this->getPassword(),
            $this->getUser()->getPassword()
        );

        if (!$isValidPassword) {
            throw new \RuntimeException(
                'Invalid credentials'
            );
        }
    }

    public function generateToken(): void
    {
        $this->setToken(
            $this->persistence->generateToken($this->user)
        );
    }
}
