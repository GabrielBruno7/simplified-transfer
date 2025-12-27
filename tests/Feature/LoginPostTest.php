<?php

namespace Tests\Feature;

use Tests\TestCase;
use Domain\ErrorCodes;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginPostTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL_LOGIN_ROUTE = '/api/login';
    private const BASE_URL_CREATE_USER_ROUTE = '/api/user';

    public function testShouldLoginSuccessfully(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '39053344705',
            'email' => 'joao.silva@test.com',
        ];

        $this->createUser($data);

        $loginData = [
            'email' => 'joao.silva@test.com',
            'senha' => 'Senha@123',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $loginData
        );

        $response->assertStatus(200);
    }

    public function testShouldThrowExceptionWhenPasswordIsWrong(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '39053344705',
            'email' => 'joao.silva@test.com',
        ];

        $this->createUser($data);

        $loginData = [
            'email' => 'joao.silva@test.com',
            'senha' => 'Senha@124',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $loginData
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_INVALID_CREDENTIALS,
                'message' => 'Credenciais inválidas',
            ])
        ;
    }

    public function testShouldThrowExceptionWhenUserNotExists(): void
    {
        $data = [
            'email' => 'joao.silva@test.com',
            'senha' => 'Senha@123',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $data
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_NOT_FOUND,
                'message' => 'Usuário não encontrado',
            ])
        ;
    }

    private function createUser(array $data): void
    {
        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response->assertStatus(201);
    }
}
