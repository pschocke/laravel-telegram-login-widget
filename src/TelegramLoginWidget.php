<?php

namespace pschocke\TelegramLoginWidget;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use pschocke\TelegramLoginWidget\Exceptions\HashValidationException;
use pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException;
use pschocke\TelegramLoginWidget\Exceptions\TelegramException;

class TelegramLoginWidget
{
    /**
     * @param $response
     * @return bool|Collection
     */
    public function validate($response)
    {
        try {
            return $this->validateWithError($response);
        } catch (TelegramException $exception) {
        }

        return false;
    }

    /**
     * @param $response
     * @return Collection
     *
     * @throws HashValidationException
     * @throws ResponseOutdatedException
     */
    public function validateWithError($response): Collection
    {
        $response = $this->convertResponseToCollection($response);

        $response = $this->checkAndGetResponseData($response);

        return $this->checkHashes($response);
    }

    private function checkAndGetResponseData(Collection $collection)
    {
        $requiredAttributes = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash'];

        return $collection->only($requiredAttributes);
    }

    private function checkHashes(Collection $collection)
    {
        $secret_key = hash('sha256', config('telegramloginwidget.bot-token'), true);

        $data = $collection->except('hash');

        $data_check_string = $data->map(function ($item, $key) {
            return $key.'='.$item;
        })
            ->values()
            ->sort()
            ->implode("\n");

        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $collection->get('hash')) !== 0) {
            throw new HashValidationException;
        }

        if (config('telegramloginwidget.validate-auth-date') && time() - $collection->get('auth_date') > 86400) {
            throw new ResponseOutdatedException;
        }

        return $data;
    }

    /**
     * @param $response
     * @return Collection
     */
    private function convertResponseToCollection($response): Collection
    {
        if ($response instanceof Request) {
            return collect($response->all());
        }

        return Collection::wrap($response);
    }
}
