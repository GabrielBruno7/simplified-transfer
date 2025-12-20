<?php

namespace Infra\Log;

use Throwable;
use Domain\ErrorCodes;
use Domain\UserException;
use Infra\Database\LogDb;
use Domain\Log\LogServiceInterface;

class LogService implements LogServiceInterface
{
    private const LEVEL_ERROR = 'error';
    private const LEVEL_WARNING = 'warning';
    private const DEFAULT_ERROR_MESSAGE = 'Erro interno no servidor';

    public function handle(Throwable $e): array
    {
        $level = $this->defineLogLevel($e);

        (new LogDb())->save($e, $level);

        return match ($e instanceof UserException) {
            true => $this->returnBodyForUserException($e),
            false => $this->returnBodyForGenericException(),
        };
    }

    private function returnBodyForGenericException(): array
    {
        return [
            'status' => 500,
            'body' => ['message' => self::DEFAULT_ERROR_MESSAGE],
        ];
    }

    private function returnBodyForUserException(UserException $e): array
    {
        return [
            'status' => 400,
            'body' => [
                'code' => $e->getCode(),
                'message' => ErrorCodes::translate($e),
            ],
        ];
    }

    private function defineLogLevel(Throwable $e): string
    {
        if ($e instanceof UserException) {
            return self::LEVEL_WARNING;
        }

        return self::LEVEL_ERROR;
    }
}
