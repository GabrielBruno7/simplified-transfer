<?php

namespace Infra\Authorizer;

use Domain\Transfer\TransferAuthorizerInterface;

class HttpTransferAuthorizer implements TransferAuthorizerInterface
{
    public function authorize(): bool
    {
        return false;
    }
}
