<?php

namespace Domain\Helper;

use Domain\ErrorCodes;
use Domain\UserException;
use Ramsey\Uuid\Uuid;

class Helper
{
    private const DOCUMENT_CPF_LENGTH = 11;
    private const DOCUMENT_CNPJ_LENGTH = 14;

    public static function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    public static function isValidUuid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    public static function checkDocument(string $document): void
    {
        $cleaned = preg_replace('/\D/', '', $document);

        $isValid = match (strlen($cleaned)) {
            self::DOCUMENT_CPF_LENGTH => self::isValidCpf($cleaned),
            self::DOCUMENT_CNPJ_LENGTH => self::isValidCnpj($cleaned),
            default => false,
        };

        if (!$isValid) {
            throw new UserException(
                ErrorCodes::USER_ERROR_INVALID_DOCUMENT,
                "The Document '{$document}' is invalid"
            );
        }
    }

    private static function isValidCpf(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }

            $digit = ((10 * $sum) % 11) % 10;
            if ($cpf[$t] != $digit) {
                return false;
            }
        }

        return true;
    }

    private static function isValidCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        $weights2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];

        foreach ([$weights1, $weights2] as $step => $weights) {
            $sum = 0;
            foreach ($weights as $i => $weight) {
                $sum += $cnpj[$i] * $weight;
            }

            $digit = $sum % 11;
            $digit = $digit < 2 ? 0 : 11 - $digit;

            if ($cnpj[12 + $step] != $digit) {
                return false;
            }
        }

        return true;
    }
}
