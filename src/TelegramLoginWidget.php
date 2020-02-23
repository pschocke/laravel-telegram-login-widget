<?php

namespace pschocke\TelegramLoginWidget;

use Illuminate\Support\Collection;
use pschocke\TelegramLoginWidget\Exceptions\HashValidationException;
use pschocke\TelegramLoginWidget\Exceptions\NotAllAttributesException;
use pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException;

class TelegramLoginWidget
{
    public function validateResponse($response): Collection
    {
        if (is_array($response)) {
            $response = collect($response);
        }

        $response = $this->checkAndGetResponseData($response);

        return $this->checkHashes($response);

    }

    private function checkAndGetResponseData(Collection $collection)
    {
        $requiredAttributes = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash'];

        $collection = $collection->only($requiredAttributes);

        if ($collection->count() != count($requiredAttributes))
            throw new NotAllAttributesException();

        return $collection;
    }

    private function checkHashes(Collection $collection)
    {
        $secret_key = hash('sha256', config('telegramloginwidget.bot-token'), true);

        $data = $collection->except('hash');

        $data_check_string = $data->map(function($item, $key) {
                return $key . '=' . $item;
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
}
