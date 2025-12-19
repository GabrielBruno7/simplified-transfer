<?php

namespace Domain\User;

interface UserPersistenceInterface
{
    public function create(User $user): User;
    public function findUserByEmailOrDocument(User $user): bool;
}
