<?php

namespace Infra\Database;

use Domain\User\User;
use Domain\User\UserPersistenceInterface;

class UserDb implements UserPersistenceInterface
{
    public function create(User $user): User
    {
        return $user;
    }
}
