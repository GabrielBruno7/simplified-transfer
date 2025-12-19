<?php

namespace Infra\Database;

use Domain\Auth\AuthPersistenceInterface;
use Domain\User\User;
use Domain\Wallet\Wallet;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Infra\Database\UserDb;
use Infra\Database\WalletDb;

class AuthDb implements AuthPersistenceInterface
{
    public function findUserByEmail(string $email): ?User
    {
        $userData = DB::table('users')
            ->select([
                'id',
                'name',
                'type',
                'email',
                'document',
                'password'
            ])
            ->where('email', $email)
            ->first();

        if (!$userData) {
            return null;
        }

        $walletData = DB::table('wallets')
            ->where('user_id', $userData->id)
            ->first();

        $wallet = (new Wallet(new WalletDb()))
            ->setId($walletData->id ?? null)
            ->setBalance($walletData->balance ?? 0.0);

        return (new User(new UserDb()))
            ->setId($userData->id)
            ->setName($userData->name)
            ->setType($userData->type)
            ->setEmail($userData->email)
            ->setDocument($userData->document)
            ->setPassword($userData->password)
            ->setWallet($wallet)
        ;
    }

    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    public function generateToken(User $user): string
    {
        $payload = [
            'iss' => config('app.url'),
            'aud' => config('app.url'),
            'iat' => now()->timestamp,
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'exp' => now()->addHours(24)->timestamp,
        ];
        
        return JWT::encode($payload, config('app.key'), 'HS256');
    }
}
