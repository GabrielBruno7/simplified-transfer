<?php

namespace App\Http\Controllers;

use Domain\Auth\Auth;
use Domain\User\User;
use Infra\Log\LogService;
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
                'senha' => 'required|string',
            ]);

            $user = (new User(new UserDb()))
                ->setEmail($validatedData['email'])
                ->loadUserByEmail()
            ;

            $auth = (new Auth(new AuthDb()))
                ->setUser($user)
                ->setPassword($validatedData['senha'])
                ->authenticate()
            ;

            return response()->json(['token' => $auth->getToken()], 200);
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
