<?php

namespace App\Http\Controllers;

use Domain\Auth\Auth;
use Domain\User\User;
use Domain\ErrorCodes;
use Domain\UserException;
use Infra\Database\AuthDb;
use Infra\Database\UserDb;
use Illuminate\Http\Request;

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
        } catch (UserException $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => ErrorCodes::translate($e),
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro interno no servidor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
