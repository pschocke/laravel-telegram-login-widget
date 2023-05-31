<?php

namespace pschocke\TelegramLoginWidget;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use pschocke\TelegramLoginWidget\Exceptions\HashValidationException;
use pschocke\TelegramLoginWidget\Exceptions\ResponseOutdatedException;
use pschocke\TelegramLoginWidget\Exceptions\TelegramException;
use TgWebValid\TgWebValid;

class TelegramLoginWidget
{
    private TgWebValid $validator;

    public function __construct()
    {
        $this->validator = new TgWebValid(config('telegramloginwidget.bot-token'));
    }

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

        return $this->checkHash($response);
    }

    /**
     * @param  Collection  $collection
     * @return Collection
     */
    private function checkAndGetResponseData(Collection $collection): Collection
    {
        $requiredAttributes = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash'];

        return $collection->only($requiredAttributes);
    }

    /**
     * @param  Collection  $collection
     * @return Collection
     *
     * @throws HashValidationException
     * @throws ResponseOutdatedException
     */
    private function checkHash(Collection $collection): Collection
    {
        $loginWidget = $this->validator->validateLoginWidget($collection->toArray());

        if (!$loginWidget) {
            throw new HashValidationException;
        }

        if (config('telegramloginwidget.validate-auth-date') && time() - $loginWidget->authDate > 86400) {
            throw new ResponseOutdatedException;
        }

        $data = $collection->except('hash');

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
