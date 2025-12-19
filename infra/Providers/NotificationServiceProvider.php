<?php

namespace Infra\Providers;

use Illuminate\Support\ServiceProvider;
use Domain\Notification\EmailSenderInterface;
use Infra\Mail\LaravelEmailSender;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            EmailSenderInterface::class,
            LaravelEmailSender::class
        );
    }
}
