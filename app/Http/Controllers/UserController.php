<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Domain\Wallet\Wallet;
use Illuminate\Http\Request;
use Infra\Database\UserDb;
use Infra\Database\WalletDb;

class UserController extends Controller
{
    public function actionCreateUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tipo' => 'nullable|string',
                'senha' => 'required|string|min:8',
                'nome' => 'required|string|max:255',
                'email' => 'required|email',
                'documento' => 'required|string',
            ]);

            $wallet = (new Wallet(new WalletDb()))->setBalance(0.0);

            $user = (new User(new UserDb()))
                ->setWallet($wallet)
                ->setName($validatedData['nome'])
                ->setEmail($validatedData['email'])
                ->setPassword($validatedData['senha'])
                ->setDocument($validatedData['documento'])
                ->setType($validatedData['tipo'] ?? User::USER_TYPE_COMMON)
                ->create()
            ;

            return response()->json(['id' => $user->getId()], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Internal Server Error'], 500); //TODO: CUSTOM ERROR
        }
    }
}