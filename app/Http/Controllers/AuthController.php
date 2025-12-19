<?php

namespace App\Http\Controllers;

use Domain\Auth\Auth;
use Domain\User\User;
use Infra\Database\AuthDb;
use Illuminate\Http\Request;
use Infra\Database\UserDb;

class AuthController extends Controller
{
    public function actionLogin(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = (new User(new UserDb()))
                ->setEmail($validatedData['email'])
                ->loadUserByEmail()
            ;

            $auth = (new Auth(new AuthDb()))
                ->setUser($user)
                ->setPassword($validatedData['password'])
                ->authenticate()
            ;

            return response()->json(['token' => $auth->getToken()], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno do servidor', //TODO CUSTOM ERROR
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
