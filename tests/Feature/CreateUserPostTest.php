<?php

namespace Tests\Feature;

use Tests\TestCase;
use Domain\ErrorCodes;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserPostTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL_CREATE_USER_ROUTE = '/api/user';

    public function testShouldCreateUserSuccessfully(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '39053344705',
            'email' => 'joao.silva@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response->assertStatus(201);
    }

    public function testShouldCreateUserSuccessfullyWithCnpj(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '98240558000149',
            'email' => 'joao.silva@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response->assertStatus(201);
    }

    public function testShouldThrowExceptionWhenCreatingUserWithInvalidType(): void
    {
        $data = [
            'tipo' => 'teste',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '98240558000149',
            'email' => 'joao.silva@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_USER_INVALID_TYPE,
                'message' => 'Tipo de usuário inválido',
            ])
        ;
    }

    public function testShouldThrowExceptionWhenCreatingUserThatAlreadyExists(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '39053344705',
            'email' => 'joao.silva@test.com',
        ];

        $this->createUser($data);

        $response = $this->post(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_ALREADY_EXISTS,
                'message' => 'O usuário já existe',
            ])
        ;
    }

    public function testShouldThrowExceptionWhenCreatingUserWithInvalidCnpj(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '12345678000111',
            'email' => 'joao.silva@test.com',
        ];

        $response = $this->post(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_INVALID_DOCUMENT,
                'message' => 'Documento inválido',
            ])
        ;
    }

    public function testShouldThrowExceptionWhenCreatingUserWithInvalidCpf(): void
    {
        $data = [
            'tipo' => 'comum',
            'senha' => 'Senha@123',
            'nome' => 'João da Silva',
            'documento' => '12345678901',
            'email' => 'joao.silva@test.com',
        ];

        $response = $this->post(
            self::BASE_URL_CREATE_USER_ROUTE,
            $data
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_INVALID_DOCUMENT,
                'message' => 'Documento inválido',
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
