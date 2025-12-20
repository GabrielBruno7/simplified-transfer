<?php

namespace Domain;

class ErrorCodes
{
    public const LANG_PT_BR = 'pt_BR';
    public const INVALID_REQUEST_MESSAGE = 'Invalid Request';

    public const USER_ERROR_TRANSFER_NOT_AUTHORIZED = 4001;
    public const USER_ERROR_INSUFFICIENT_FUNDS = 4002;
    public const USER_ERROR_SAME_USER_TRANSFER = 4003;
    public const USER_ERROR_MERCHANT_CANNOT_TRANSFER = 4004;
    public const USER_ERROR_INVALID_CREDENTIALS = 4005;
    public const USER_NOT_FOUND = 4006;
    public const USER_USER_INVALID_TYPE = 4007;
    public const USER_ERROR_ALREADY_EXISTS = 4008;
    public const USER_ERROR_TRANSFER_VALUE_MUST_BE_GREATER_THAN_ZERO = 4009;
    public const USER_ERROR_WALLET_NOT_FOUND = 4010;
    public const USER_ERROR_WALLET_BALANCE_CANNOT_BE_NEGATIVE = 4011;

    public const TRANSLATIONS_PT_BR = [
        self::USER_NOT_FOUND => 'Usuário não encontrado',
        self::USER_USER_INVALID_TYPE => 'Tipo de usuário inválido',
        self::USER_ERROR_INVALID_CREDENTIALS => 'Credenciais inválidas',
        self::USER_ERROR_INSUFFICIENT_FUNDS => 'Saldo insuficiente para realizar a transferência',
        self::USER_ERROR_MERCHANT_CANNOT_TRANSFER => 'Lojistas não podem realizar transferências',
        self::USER_ERROR_SAME_USER_TRANSFER => 'Pagador e beneficiário não podem ser o mesmo usuário',
        self::USER_ERROR_TRANSFER_NOT_AUTHORIZED => 'Transferência não autorizada pelo serviço externo',
        self::USER_ERROR_ALREADY_EXISTS => 'O usuário já existe',
        self::USER_ERROR_TRANSFER_VALUE_MUST_BE_GREATER_THAN_ZERO => 'O valor da transferência deve ser maior que zero',
        self::USER_ERROR_WALLET_NOT_FOUND => 'Carteira não encontrada para o usuário',
        self::USER_ERROR_WALLET_BALANCE_CANNOT_BE_NEGATIVE => 'O saldo da carteira não pode ser negativo',
    ];

    public const TRANSLATIONS = [
        self::LANG_PT_BR => self::TRANSLATIONS_PT_BR,
    ];

    public static function translate(
        \Throwable $e,
        string $lang = self::LANG_PT_BR
    ): string {
        $noTranslation = 'Mensagem de erro sem tradução. Entre em contato com o suporte';

        if (! $e instanceof UserExceptionInterface) {
            return $noTranslation;
        }

        $code = $e->getErrorCode();
        $data = $e->getData();

        $translation = self::TRANSLATIONS[$lang][$code] ?? $noTranslation;

        if (!empty($data)) {
            $translation .= ' - ' . json_encode($data);
        }

        return $translation;
    }
}
