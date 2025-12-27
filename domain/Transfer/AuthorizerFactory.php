<?php

namespace Domain\Transfer;

use Infra\Authorizer\TransferAuthorizer;
use Infra\Authorizer\HttpTransferAuthorizer;

class AuthorizerFactory
{
    private const ENVIROMENT_LOCAL = 'local';
    private const ENVIROMENT_TESTING = 'testing';

    public static function make(): TransferAuthorizerInterface
    {
        if (app()->bound(TransferAuthorizerInterface::class)) {
            return app(TransferAuthorizerInterface::class);
        }

        return match(app()->environment(self::ENVIROMENT_LOCAL, self::ENVIROMENT_TESTING)) {
            true => new TransferAuthorizer(),
            false => new HttpTransferAuthorizer(),
        };
    }
}
