<?php

namespace Infra\Database;

use Throwable;
use Domain\Helper\Helper;
use Illuminate\Support\Facades\DB;

class LogDb
{
    public function save(Throwable $e, string $level): void
    {
        DB::table('logs')->insert([
            'id' => Helper::generateUuid(),
            'level' => $level,
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'context' => json_encode([
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]),
            'environment' => app()->environment(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
