<?php

namespace App\Http\Controllers;

use Domain\Auth\Auth;
use Domain\User\User;
use Domain\ErrorCodes;
use Domain\UserException;
use Infra\Database\AuthDb;
use Infra\Database\UserDb;
use Illuminate\Http\Request;
use Infra\Log\LogService;

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
