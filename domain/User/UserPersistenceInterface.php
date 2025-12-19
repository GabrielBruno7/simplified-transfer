<?php

namespace Domain\User;

interface UserPersistenceInterface
{
    public function create(User $user): User;
    public function findUserByEmail(User $user): bool;
    public function findUserByEmailOrDocument(User $user): bool;
    public function findUserByDocument(User $user): bool;
}
