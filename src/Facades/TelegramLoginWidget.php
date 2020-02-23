<?php

namespace pschocke\TelegramLoginWidget\Facades;

use Illuminate\Support\Facades\Facade;

class TelegramLoginWidget extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \pschocke\TelegramLoginWidget\TelegramLoginWidget::class;
    }
}
