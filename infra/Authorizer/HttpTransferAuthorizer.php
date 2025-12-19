<?php

namespace Infra\Authorizer;

use Domain\Transfer\TransferAuthorizerInterface;

class HttpTransferAuthorizer implements TransferAuthorizerInterface
{
    public function authorize(): bool
    {
        //TODO: Implement a real HTTP request to an external authorization service

        return false;
    }
}
