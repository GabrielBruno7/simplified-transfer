<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Illuminate\Http\Request;
use Infra\Database\UserDb;

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

            $user = (new User(new UserDb()))
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
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}