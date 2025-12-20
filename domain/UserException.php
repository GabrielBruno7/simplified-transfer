<?php

namespace Domain;

use RuntimeException;

class UserException extends \RuntimeException implements UserExceptionInterface
{
    public function __construct(
        int $code,
        string $message,
        private ?array $data = null
    ) {
        parent::__construct($message, $code);
    }

    public function getErrorCode(): int
    {
        return $this->getCode();
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
