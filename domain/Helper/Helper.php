<?php

namespace Domain\Helper;

use Ramsey\Uuid\Uuid;

class Helper
{
    public static function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    public static function isValidUuid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }
}
