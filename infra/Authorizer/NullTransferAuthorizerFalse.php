<?php

namespace Infra\Authorizer;

use Domain\Transfer\TransferAuthorizerInterface;

class NullTransferAuthorizerFalse implements TransferAuthorizerInterface
{
    public function authorize(): bool
    {
        return false;
    }
}
