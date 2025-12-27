<?php

namespace Tests\Feature;

use Tests\TestCase;
use Domain\ErrorCodes;
use Domain\Transfer\TransferAuthorizerInterface;
use Infra\Authorizer\NullTransferAuthorizerTrue;
use Infra\Authorizer\NullTransferAuthorizerFalse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferPostTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL_LOGIN_ROUTE = '/api/login';
    private const BASE_URL_CREATE_USER_ROUTE = '/api/user';
    private const BASE_URL_TRANSFER_ROUTE = '/api/transfer';
    private const BASE_URL_ADD_BALANCE_ROUTE = '/api/user/{document}/add-balance';

    public function testShouldTransferSuccessfully(): void
    {
        $this->app->bind(
            TransferAuthorizerInterface::class,
            fn () => new NullTransferAuthorizerTrue()
        );

        $payerDocument = '38341062089';
        $payeeDocument = '85705720009';

        $userCommonData = [
            'tipo' => 'comum',
            'senha' => '12345678',
            'nome' => 'Lucas Moreira',
            'documento' => $payerDocument,
            'email' => 'lucas.moreira@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userCommonData
        );

        $response->assertStatus(201);

        $userMerchantData = [
            'tipo' => 'lojista',
            'senha' => '12345678',
            'nome' => 'Vanessa Souza',
            'documento' => $payeeDocument,
            'email' => 'vanessa.souza@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userMerchantData
        );

        $response->assertStatus(201);

        $loginData = [
            'email' => 'vanessa.souza@test.com',
            'senha' => '12345678',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $loginData
        );

        $response->assertStatus(200);

        $token = $response->json('token');

        $response = $this->POST(
            str_replace('{document}', $payerDocument, self::BASE_URL_ADD_BALANCE_ROUTE),
            ['valor' => '500.00'],
            ['Authorization' => 'Bearer ' . $token],
        );

        $response->assertStatus(200);

        $transferData = [
            'valor' => '100.00',
            'pagador' => $payerDocument,
            'recebedor' => $payeeDocument,
        ];

        $response = $this->POST(
            self::BASE_URL_TRANSFER_ROUTE,
            $transferData,
            ['Authorization' => 'Bearer ' . $token],
        );

        $response->assertStatus(200);
    }

    public function testShouldThrowExceptionWhenAuthorizerExternalServerDontApproveTheTransfer(): void
    {
        $this->app->bind(
            TransferAuthorizerInterface::class,
            fn () => new NullTransferAuthorizerFalse()
        );

        $payerDocument = '38341062089';
        $payeeDocument = '85705720009';

        $userCommonData = [
            'tipo' => 'comum',
            'senha' => '12345678',
            'nome' => 'Lucas Moreira',
            'documento' => $payerDocument,
            'email' => 'lucas.moreira@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userCommonData
        );

        $response->assertStatus(201);

        $userMerchantData = [
            'tipo' => 'lojista',
            'senha' => '12345678',
            'nome' => 'Vanessa Souza',
            'documento' => $payeeDocument,
            'email' => 'vanessa.souza@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userMerchantData
        );

        $response->assertStatus(201);

        $loginData = [
            'email' => 'vanessa.souza@test.com',
            'senha' => '12345678',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $loginData
        );

        $response->assertStatus(200);

        $token = $response->json('token');

        $response = $this->POST(
            str_replace('{document}', $payerDocument, self::BASE_URL_ADD_BALANCE_ROUTE),
            ['valor' => '500.00'],
            ['Authorization' => 'Bearer ' . $token],
        );

        $response->assertStatus(200);

        $transferData = [
            'valor' => '100.00',
            'pagador' => $payerDocument,
            'recebedor' => $payeeDocument,
        ];

        $response = $this->POST(
            self::BASE_URL_TRANSFER_ROUTE,
            $transferData,
            ['Authorization' => 'Bearer ' . $token],
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_TRANSFER_NOT_AUTHORIZED,
                'message' => 'Transferência não autorizada pelo serviço externo',
            ])
        ;
    }

    public function testShouldThrowExceptionWhenPayerDontHaveBalance(): void
    {
        $this->app->bind(
            TransferAuthorizerInterface::class,
            fn () => new NullTransferAuthorizerTrue()
        );

        $payerDocument = '38341062089';
        $payeeDocument = '85705720009';

        $userCommonData = [
            'tipo' => 'comum',
            'senha' => '12345678',
            'nome' => 'Lucas Moreira',
            'documento' => $payerDocument,
            'email' => 'lucas.moreira@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userCommonData
        );

        $response->assertStatus(201);

        $userMerchantData = [
            'tipo' => 'lojista',
            'senha' => '12345678',
            'nome' => 'Vanessa Souza',
            'documento' => $payeeDocument,
            'email' => 'vanessa.souza@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userMerchantData
        );

        $response->assertStatus(201);

        $loginData = [
            'email' => 'vanessa.souza@test.com',
            'senha' => '12345678',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $loginData
        );

        $response->assertStatus(200);

        $token = $response->json('token');

        $transferData = [
            'valor' => '100.00',
            'pagador' => $payerDocument,
            'recebedor' => $payeeDocument,
        ];

        $response = $this->POST(
            self::BASE_URL_TRANSFER_ROUTE,
            $transferData,
            ['Authorization' => 'Bearer ' . $token],
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_INSUFFICIENT_FUNDS,
                'message' => 'Saldo insuficiente para realizar a transferência',
            ])
        ;
    }

        public function testShouldThrowExceptionWhenMerchantUserTrySendFunds(): void
    {
        $this->app->bind(
            TransferAuthorizerInterface::class,
            fn () => new NullTransferAuthorizerTrue()
        );

        $payerDocument = '38341062089';
        $payeeDocument = '85705720009';

        $userCommonData = [
            'tipo' => 'comum',
            'senha' => '12345678',
            'nome' => 'Lucas Moreira',
            'documento' => $payerDocument,
            'email' => 'lucas.moreira@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userCommonData
        );

        $response->assertStatus(201);

        $userMerchantData = [
            'tipo' => 'lojista',
            'senha' => '12345678',
            'nome' => 'Vanessa Souza',
            'documento' => $payeeDocument,
            'email' => 'vanessa.souza@test.com',
        ];

        $response = $this->POST(
            self::BASE_URL_CREATE_USER_ROUTE,
            $userMerchantData
        );

        $response->assertStatus(201);

        $loginData = [
            'email' => 'vanessa.souza@test.com',
            'senha' => '12345678',
        ];

        $response = $this->POST(
            self::BASE_URL_LOGIN_ROUTE,
            $loginData
        );

        $response->assertStatus(200);

        $token = $response->json('token');

        $response = $this->POST(
            str_replace('{document}', $payerDocument, self::BASE_URL_ADD_BALANCE_ROUTE),
            ['valor' => '500.00'],
            ['Authorization' => 'Bearer ' . $token],
        );

        $response->assertStatus(200);

        $transferData = [
            'valor' => '100.00',
            'pagador' => $payeeDocument,
            'recebedor' => $payerDocument
        ];

        $response = $this->POST(
            self::BASE_URL_TRANSFER_ROUTE,
            $transferData,
            ['Authorization' => 'Bearer ' . $token],
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'code' => ErrorCodes::USER_ERROR_MERCHANT_CANNOT_TRANSFER,
                'message' => 'Lojistas não podem realizar transferências',
            ])
        ;
    }
}
