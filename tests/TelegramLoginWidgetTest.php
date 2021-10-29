<?php

namespace pschocke\TelegramLoginWidget\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use pschocke\TelegramLoginWidget\Exceptions\HashValidationException;
use pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException;
use pschocke\TelegramLoginWidget\Facades\TelegramLoginWidget;
use pschocke\TelegramLoginWidget\TelegramLoginWidget as NormalTelegramLoginWidget;
use Throwable;

class TelegramLoginWidgetTest extends TestCase
{
    private $payload;

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

    /** @test */
    public function it_can_validate_a_valid_response_via_the_hash()
    {
        $this->assertInstanceOf(Collection::class, (new NormalTelegramLoginWidget())->validate($this->payload));
    }

    /** @test */
    public function it_can_validate_an_invalid_response_via_the_hash()
    {
        $this->payload['id'] = 1;
        $this->expectException(HashValidationException::class);
        TelegramLoginWidget::validateWithError($this->payload);
        $this->assertFalse(TelegramLoginWidget::validate($this->payload));
    }

    /** @test */
    public function it_works_with_optional_fields_in_response()
    {
        $this->payload['last_name'] = '';
        $this->generateHash();
        $this->assertInstanceOf(Collection::class, TelegramLoginWidget::validate($this->payload));
    }

    /** @test */
    public function it_ignores_other_parameter()
    {
        $this->payload['test'] = 2;
        $validTelegramData = TelegramLoginWidget::validate($this->payload);
        $this->assertEmpty($validTelegramData->get('test'));
    }

    /** @test */
    public function it_checks_the_auth_date_by_default()
    {
        $this->payload['auth_date'] = time() - 100000;
        $this->generateHash();

        $this->expectException(ResponseOutdatedException::class);
        TelegramLoginWidget::validateWithError($this->payload);
        $this->assertFalse(TelegramLoginWidget::validate($this->payload));
    }

    /** @test */
    public function it_ignores_the_auth_date_when_configured()
    {
        $this->payload['auth_date'] = time() - 100000;
        $this->generateHash();

        config(['telegramloginwidget.validate-auth-date' => false]);

        $this->assertInstanceOf(Collection::class, TelegramLoginWidget::validate($this->payload));
    }

    /** @test */
    public function it_can_handle_a_response_as_function_parameter()
    {
        $data = new Request();
        $data->replace($this->payload);
        $this->assertInstanceOf(Collection::class, TelegramLoginWidget::validate($data));
    }

    /** @test */
    public function it_sets_the_correct_values_in_the_collection()
    {
        $telegramUser = (new NormalTelegramLoginWidget())->validate($this->payload);

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
                $data_check_arr[] = $key.'='.$value;
            }
        }

        sort($data_check_arr);

        $this->payload['hash'] = hash_hmac('sha256', implode("\n", $data_check_arr), hash('sha256', config('telegramloginwidget.bot-token'), true));
    }
}
