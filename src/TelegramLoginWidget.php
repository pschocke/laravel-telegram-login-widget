<?php

namespace pschocke\TelegramLoginWidget;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use pschocke\TelegramLoginWidget\Exceptions\HashValidationException;
use pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException;
use pschocke\TelegramLoginWidget\Exceptions\TelegramException;

final readonly class TelegramLoginWidget
{
    /**
     * @param array<string, mixed>|Request|Collection<string, mixed> $response
     * @return bool|Collection<string, mixed>
     */
    public function validate(mixed $response): bool|Collection
    {
        try {
            return $this->validateWithError($response);
        } catch (TelegramException) {
        }

        return false;
    }

    /**
     * @param array<string, mixed>|Request|Collection<string, mixed> $response
     * @return Collection<string, mixed>
     *
     * @throws HashValidationException
     * @throws ResponseOutdatedException
     */
    public function validateWithError(mixed $response): Collection
    {
        $response = $this->convertResponseToCollection($response);

        $response = $this->checkAndGetResponseData($response);

        return $this->checkHash($response);
    }

    /**
     * @param  Collection<string, mixed>  $collection
     * @return Collection<string, mixed>
     */
    private function checkAndGetResponseData(Collection $collection): Collection
    {
        $requiredAttributes = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash'];

        return $collection->only($requiredAttributes);
    }

    /**
     * @param  Collection<string, mixed>  $collection
     * @return Collection<string, mixed>
     *
     * @throws HashValidationException
     * @throws ResponseOutdatedException
     */
    private function checkHash(Collection $collection): Collection
    {
        $botToken = config('telegramloginwidget.bot-token');
        if (!is_string($botToken)) {
            throw new HashValidationException('Telegram bot token is not configured.');
        }

        $secret_key = hash('sha256', $botToken, true);

        $data = $collection->except('hash');

        $data_check_string = $data->map(function (mixed $item, string $key): string {
            if (!is_string($item) && !is_numeric($item)) {
                return $key.'=';
            }

            return $key.'='.(string) $item;
        })
            ->values()
            ->sort()
            ->implode("\n");

        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        $hashToCompare = $collection->get('hash');
        if (!is_string($hashToCompare) || !hash_equals($hash, $hashToCompare)) {
            throw new HashValidationException;
        }

        if (config('telegramloginwidget.validate-auth-date')) {
            $authDate = $collection->get('auth_date');
            if (is_numeric($authDate) && time() - (int) $authDate > 86400) {
                throw new ResponseOutdatedException;
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed>|Request|Collection<string, mixed> $response
     * @return Collection<string, mixed>
     */
    private function convertResponseToCollection(mixed $response): Collection
    {
        if ($response instanceof Request) {
            /** @var array<string, mixed> $all */
            $all = $response->all();

            return collect($all);
        }

        /** @var array<string, mixed>|Collection<string, mixed> $response */
        return Collection::wrap($response);
    }
}
