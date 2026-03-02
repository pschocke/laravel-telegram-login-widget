<?php

namespace pschocke\TelegramLoginWidget\Facades;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool|Collection validate(array|Request|Collection $collection)
 * @method static Collection validateWithError(array|Request|Collection $collection)
 */
class TelegramLoginWidget extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \pschocke\TelegramLoginWidget\TelegramLoginWidget::class;
    }
}
