<?php

namespace Infra\Database;

use Domain\User\User;
use Domain\Wallet\Wallet;
use Illuminate\Support\Facades\DB;
use Domain\User\UserPersistenceInterface;

class UserDb implements UserPersistenceInterface
{
    public function create(User $user): User
    {
        DB::table('users')->insert([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'type' => $user->getType(),
            'email' => $user->getEmail(),
            'document' => $user->getDocument(),
            'password' => $user->getPassword(),
        ]);

        return $user;
    }

    public function findUserByEmailOrDocument(User $user): bool
    {
        $result = DB::table('users')
            ->select([
                'id',
                'name',
                'type',
                'email',
                'document',
                'password',
            ])
            ->where('email', $user->getEmail())
            ->orWhere('document', $user->getDocument())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $user
            ->setId($result->id)
            ->setName($result->name)
            ->setType($result->type)
            ->setEmail($result->email)
            ->setDocument($result->document)
            ->setPassword($result->password)
        ;

        return true;
    }

    public function findUserByEmail(User $user): bool
    {
        $result = DB::table('users')
            ->select([
                'id',
                'name',
                'type',
                'email',
                'document',
                'password',
            ])
            ->where('email', $user->getEmail())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $user
            ->setId($result->id)
            ->setName($result->name)
            ->setType($result->type)
            ->setEmail($result->email)
            ->setDocument($result->document)
            ->setPassword($result->password)
        ;

        return true;
    }

    public function findUserByDocument(User $user): bool
    {
        $result = DB::table('users')
            ->select([
                'id',
                'name',
                'type',
                'email',
                'document',
                'password',
            ])
            ->where('document', $user->getDocument())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $user
            ->setId($result->id)
            ->setName($result->name)
            ->setType($result->type)
            ->setEmail($result->email)
            ->setDocument($result->document)
            ->setPassword($result->password)
        ;

        $wallet = (new Wallet(new WalletDb()))
            ->setUser($user)
            ->loadByUser()
        ;

        $user->setWallet($wallet);

        return true;
    }
}
