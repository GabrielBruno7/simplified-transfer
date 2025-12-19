<?php

namespace Domain\Auth;

use Domain\User\User;

interface AuthPersistenceInterface
{
    public function findUserByEmail(string $email): ?User;
    public function verifyPassword(string $password, string $hashedPassword): bool;
    public function generateToken(User $user): string;
}
