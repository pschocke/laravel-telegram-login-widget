<?php

namespace pschocke\TelegramLoginWidget\Facades;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool|Collection<string, mixed> validate(array<string, mixed>|Request|Collection<string, mixed> $collection)
 * @method static Collection<string, mixed> validateWithError(array<string, mixed>|Request|Collection<string, mixed> $collection)
 */
class TelegramLoginWidget extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \pschocke\TelegramLoginWidget\TelegramLoginWidget::class;
    }
}
