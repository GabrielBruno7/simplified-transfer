<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Infra\Log\LogService;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $authHeader = $request->header('Authorization');
            
            if (!$authHeader) {
                return response()->json([
                    'error' => 'Token de autorização não fornecido'
                ], 401);
            }

            if (!str_starts_with($authHeader, 'Bearer ')) {
                return response()->json([
                    'error' => 'Formato do token inválido. Use Bearer <token>'
                ], 401);
            }

            $token = substr($authHeader, 7);

            $decoded = JWT::decode($token, new Key(config('app.key'), 'HS256'));

            $user = DB::table('users')->where('id', $decoded->user_id)->first();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não encontrado'
                ], 401);
            }

            $request->attributes->set('user_id', $decoded->user_id);
            $request->attributes->set('user_email', $decoded->email);
            $request->attributes->set('user', $user);

            return $next($request);
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