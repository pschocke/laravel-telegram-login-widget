# TelegramLoginWidget

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
![run-tests](https://github.com/pschocke/laravel-telegram-login-widget/workflows/run-tests/badge.svg?branch=master)
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![StyleCI][ico-styleci]][link-styleci]
[![codecov][ico-codecov]][link-codecov]

Laravel Telegram Login Widget. Easily integrate Telegrams login widget to send Telegram messages.

You can view a full video of the installation process an usage [here](https://www.youtube.com/watch?v=tUATaHDW6FY), where we build an app that sends Telegram notifications from start to finish.

## Installation

Via Composer

``` bash
composer require pschocke/laravel-telegram-login-widget
```

Then publish the configuration file
``` bash
php artisan vendor:publish --tag=telegramloginwidget.config
```

## Usage

First you have to [create a bot](https://core.telegram.org/bots#3-how-do-i-create-a-bot) at Telegram.
After that [set up your login widget](https://core.telegram.org/widgets/login) in your frontend.

Create an env variable `TELEGRAM_BOT_TOKEN` with your bots token

Create a route to handle the callback/redirect after the the successful connection between the user account and 
your telegram bot. Telegram uses a hash to allow you to verify the response is from Telegram. Here comes this package in play:

```php
use pschocke\TelegramLoginWidget\Facades\TelegramLoginWidget;

class TelegramCallbackController extends Controller {

    public function __invoke(Request $request) {
        if($telegramUser = TelegramLoginWidget::validate($request)) {
            // user is valid
        }
        // telegram response is not valid. Account connection failed
    }
}
```

if you want more control over the response, you can use the `validateWithError()` method to catch more fine tuned errors: 

```php
use pschocke\TelegramLoginWidget\Facades\TelegramLoginWidget;

class TelegramCallbackController extends Controller {

    public function __invoke(Request $request) {
        $telegramUser = [];
        try {
            $telegramUser = TelegramLoginWidget::validateWithError($request);
        } catch(pschocke\TelegramLoginWidget\Exceptions\HashValidationException $e) {
            // the response is not from telegram
        } catch(pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException $e) {
            // the response is outdated.
        }
    }
}
```

At this stage, `$telegramUser` contains a collection of all attributes Telegram provides: id, first_name, last_name, username, photo_url and auth_date.

```php
echo $telegramUser->first_name; // Max
echo $telegramUser->last_name; // Mustermann 
```

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
