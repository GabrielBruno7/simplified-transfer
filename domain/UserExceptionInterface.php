<?php

namespace Domain;

interface UserExceptionInterface
{
    public function getErrorCode(): int;
    public function getData(): array|null;
}
