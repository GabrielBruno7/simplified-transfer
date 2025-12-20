<?php

namespace Infra\Authorizer;

use Domain\Transfer\TransferAuthorizerInterface;

class TransferAuthorizer implements TransferAuthorizerInterface
{
    public function authorize(): bool
    {
        return random_int(0, 1) === 1;
    }
}
