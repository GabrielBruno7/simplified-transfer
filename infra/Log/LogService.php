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
            false => $this->returnBodyForGenericException($e),
        };
    }

    private function returnBodyForGenericException(Throwable $e): array
    {
        $body = ['message' => self::DEFAULT_ERROR_MESSAGE];

        if (app()->environment('local', 'testing')) {
            $body['exception'] = class_basename($e);
            $body['error'] = $e->getMessage();
            $body['trace'] = $this->cleanTrace($e);
        }

        return [
            'status' => 500,
            'body' => $body,
        ];
    }

    private function cleanTrace(Throwable $e): array
    {
        return collect($e->getTrace())
            ->filter(fn ($frame) =>
                isset($frame['file']) &&
                (
                    str_contains($frame['file'], '/app/') ||
                    str_contains($frame['file'], '/Domain/') ||
                    str_contains($frame['file'], '/Infra/')
                )
            )
            ->map(fn ($frame) => [
                'file' => str_replace(base_path(), '', $frame['file']),
                'line' => $frame['line'] ?? null,
                'function' => $frame['function'] ?? null,
                'class' => $frame['class'] ?? null,
            ])
            ->values()
            ->toArray()
        ;
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
