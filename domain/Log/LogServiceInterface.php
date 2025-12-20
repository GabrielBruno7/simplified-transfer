<?php 

namespace Domain\Log;

use Throwable;

interface LogServiceInterface
{
    public function handle(Throwable $e): array;
}
