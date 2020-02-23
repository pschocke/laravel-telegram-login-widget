<?php

namespace pschocke\TelegramLoginWidget\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use pschocke\TelegramLoginWidget\TelegramLoginWidgetServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [TelegramLoginWidgetServiceProvider::class];
    }
}
