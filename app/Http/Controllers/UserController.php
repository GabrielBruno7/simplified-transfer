<?php

namespace App\Http\Controllers;

use Domain\User\User;
use Illuminate\Http\Request;
use Infra\Database\UserDb\UserDb;

class UserController extends Controller
{
    public function actionCreateUser(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'documento' => 'required|string|unique:users,document',
                'senha' => 'required|string|min:8',
                'tipo' => 'nullable|string',
            ]);

            $user = (new User(new UserDb()))
                ->setName($validatedData['nome'])
                ->setType($validatedData['tipo'])
                ->setEmail($validatedData['email'])
                ->setPassword($validatedData['senha'])
                ->setDocument($validatedData['documento'])
                ->create()
            ;

            return response()->json(['id' => $user->getId()], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}