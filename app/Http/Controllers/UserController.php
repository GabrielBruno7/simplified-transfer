<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Domain\ErrorCodes;
use Domain\UserException;
use Domain\Wallet\Wallet;
use Infra\Database\UserDb;
use Illuminate\Http\Request;
use Infra\Database\WalletDb;
use Infra\Log\LogService;

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

            $wallet = (new Wallet(new WalletDb()));

            $user = (new User(new UserDb()))
                ->setWallet($wallet)
                ->setName($validatedData['nome'])
                ->setEmail($validatedData['email'])
                ->setDocument($validatedData['documento'])
                ->setPassword(bcrypt($validatedData['senha']))
                ->setType($validatedData['tipo'] ?? User::USER_TYPE_COMMON)
                ->create()
            ;

            return response()->json(['id' => $user->getId()], 201);
        } catch (\Throwable $e) {
            $logService = new LogService();
            $response = $logService->handle($e);

            return response()->json(
                $response['body'],
                $response['status']
            );
        }
    }
}