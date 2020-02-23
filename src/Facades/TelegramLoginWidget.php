<?php

namespace pschocke\TelegramLoginWidget\Facades;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Class TelegramLoginWidget
 * @method static Collection validateResponse(array|Request|Collection $collection)
 */
class TelegramLoginWidget extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \pschocke\TelegramLoginWidget\TelegramLoginWidget::class;
    }
}
