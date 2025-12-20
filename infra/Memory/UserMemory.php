<?php

namespace Infra\Memory;

use Domain\User\User;
use Domain\User\UserPersistenceInterface;

class UserMemory implements UserPersistenceInterface
{
    public function create(User $user): User
    {
        return $user;
    }

    public function findUserByEmail(User $user): bool
    {
        return false;
    }

    public function findUserByDocument(User $user): bool
    {
        return false;
    }

    public function findUserByEmailOrDocument(User $user): bool
    {
        if ($user->getDocument() === '12345678901') {
            return true;
        }

        return false;
    }
}
