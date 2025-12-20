<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'error' => 'Token expirado'
            ], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json([
                'error' => 'Assinatura do token inválida'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token inválido',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}