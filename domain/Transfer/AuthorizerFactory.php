<?php

namespace Domain\Transfer;

use Infra\Authorizer\FakeTransferAuthorizer;
use Infra\Authorizer\HttpTransferAuthorizer;

class AuthorizerFactory
{
    private const ENVIROMENT_LOCAL = 'local';
    private const ENVIROMENT_TESTING = 'testing';

    public static function make(): TransferAuthorizerInterface
    {
        return match(app()->environment(self::ENVIROMENT_LOCAL, self::ENVIROMENT_TESTING)) {
            true => new FakeTransferAuthorizer(),
            false => new HttpTransferAuthorizer(),
        };
    }
}
