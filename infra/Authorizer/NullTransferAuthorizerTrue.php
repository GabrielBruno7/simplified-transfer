<?php

namespace Infra\Authorizer;

use Domain\Transfer\TransferAuthorizerInterface;

class NullTransferAuthorizerTrue implements TransferAuthorizerInterface
{
    public function authorize(): bool
    {
        return true;
    }
}
