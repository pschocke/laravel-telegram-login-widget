# TelegramLoginWidget

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![StyleCI][ico-styleci]][link-styleci]
[![codecov][ico-codecov]][link-codecov]

Laravel Telegram Login Widget. Easily integrate Telegrams login widget to send Telegram messages.

## Installation

Via Composer

``` bash
$ composer require pschocke/laravel-telegram-login-widget
```

## Usage

First you have to [create a bot](https://core.telegram.org/bots#3-how-do-i-create-a-bot) at Telegram.
After that [set up your login widget](https://core.telegram.org/widgets/login) in your frontend.

Create an env variable `TELEGRAM_BOT_TOKEN` with your bots token

Create a route to handle the callback/redirect after the the successful connection between the user account and 
your telegram bot. Telegram uses a hash to allow you to verify the response is from Telegram. Here comes this package in play:

```php

class TelegramCallbackController extends Controller {

    public function __invoke(Request $request, TelegramLoginWidget $widget) {
        $telegramUser;
        
        try {
            $telegramUser = $widget->validateResponse($request);
        } catch(pschocke\TelegramLoginWidget\Exceptions\HashValidationException $e) {
            // the response is not from telegram
        } catch(pschocke\TelegramLoginWidget\Exceptions\NotAllAttributesException $e) {
            // the response doens't contain all userdata
        } catch(pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException $e) {
            // the response is outdated.
        }
    }
}

```

At this stage, `$telegramUser` contains a collection of all attributes Telegram provides: id, first_name, last_name, username, photo_url and auth_date.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Patrick Schocke][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/pschocke/laravel-telegram-login-widget.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/pschocke/laravel-telegram-login-widget.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/pschocke/laravel-telegram-login-widget/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/242549196/shield
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/pschocke/laravel-telegram-login-widget.svg?style=flat-square
[ico-codecov]: https://codecov.io/gh/pschocke/laravel-telegram-login-widget/branch/master/graph/badge.svg

[link-packagist]: https://packagist.org/packages/pschocke/laravel-telegram-login-widget
[link-downloads]: https://packagist.org/packages/pschocke/laravel-telegram-login-widget
[link-travis]: https://travis-ci.org/pschocke/laravel-telegram-login-widget
[link-styleci]: https://styleci.io/repos/242549196
[link-scrutinizer]: https://scrutinizer-ci.com/g/pschocke/laravel-telegram-login-widget
[link-codecov]: https://codecov.io/gh/pschocke/laravel-telegram-login-widget
[link-author]: https://github.com/pschocke
[link-contributors]: ../../contributors
