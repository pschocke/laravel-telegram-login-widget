<?php

namespace pschocke\TelegramLoginWidget\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use pschocke\TelegramLoginWidget\Exceptions\HashValidationException;
use pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException;
use pschocke\TelegramLoginWidget\Facades\TelegramLoginWidget;
use pschocke\TelegramLoginWidget\TelegramLoginWidget as NormalTelegramLoginWidget;

class TelegramLoginWidgetTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $payload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'id' => 123,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'JohnDoe',
            'photo_url' => 'https://google.com',
            'auth_date' => time(),
        ];

        $this->generateHash();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_validate_a_valid_response_via_the_hash(): void
    {
        $this->assertInstanceOf(Collection::class, (new NormalTelegramLoginWidget())->validate($this->payload));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_validate_an_invalid_response_via_the_hash(): void
    {
        $this->payload['id'] = 1;
        $this->expectException(HashValidationException::class);
        TelegramLoginWidget::validateWithError($this->payload);
        $this->assertFalse(TelegramLoginWidget::validate($this->payload));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_optional_fields_in_response(): void
    {
        $this->payload['last_name'] = '';
        $this->generateHash();
        $this->assertInstanceOf(Collection::class, TelegramLoginWidget::validate($this->payload));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_ignores_other_parameter(): void
    {
        $this->payload['test'] = 2;
        $validTelegramData = TelegramLoginWidget::validate($this->payload);
        $this->assertInstanceOf(Collection::class, $validTelegramData);
        $this->assertEmpty($validTelegramData->get('test'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_checks_the_auth_date_by_default(): void
    {
        $this->payload['auth_date'] = time() - 100000;
        $this->generateHash();

        $this->expectException(ResponseOutdatedException::class);
        TelegramLoginWidget::validateWithError($this->payload);
        $this->assertFalse(TelegramLoginWidget::validate($this->payload));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_ignores_the_auth_date_when_configured(): void
    {
        $this->payload['auth_date'] = time() - 100000;
        $this->generateHash();

        config(['telegramloginwidget.validate-auth-date' => false]);

        $this->assertInstanceOf(Collection::class, TelegramLoginWidget::validate($this->payload));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_a_response_as_function_parameter(): void
    {
        $data = new Request();
        $data->replace($this->payload);
        $this->assertInstanceOf(Collection::class, TelegramLoginWidget::validate($data));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_the_correct_values_in_the_collection(): void
    {
        $telegramUser = (new NormalTelegramLoginWidget())->validate($this->payload);
        $this->assertInstanceOf(Collection::class, $telegramUser);

        $actual = $this->payload;
        unset($actual['hash']);

        foreach ($actual as $key => $value) {
            $this->assertSame($value, $telegramUser->get($key));
        }
    }

    private function generateHash(): void
    {
        $data_check_arr = [];

        foreach ($this->payload as $key => $value) {
            if (in_array($key, ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date'])) {
                $item = (is_string($value) || is_numeric($value)) ? (string) $value : '';
                $data_check_arr[] = $key.'='.$item;
            }
        }

        sort($data_check_arr);

        $botToken = config('telegramloginwidget.bot-token');
        $botToken = is_string($botToken) ? $botToken : '';

        $this->payload['hash'] = hash_hmac('sha256', implode("\n", $data_check_arr), hash('sha256', $botToken, true));
    }
}
