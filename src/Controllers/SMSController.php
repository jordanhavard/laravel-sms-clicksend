<?php

namespace JordanHavard\ClickSend\Controllers;

use ClickSendLib\APIHelper;
use ClickSendLib\Configuration;
use Exception;
use Illuminate\Support\Facades\Http;
use JordanHavard\ClickSend\Exceptions\APIException;

class SMSController extends BaseController
{
    /**
     * @var \ClickSendLib\Controllers\SMSController The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return SMSController The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @throws APIException|Exception Thrown if API call fails
     */
    public function sendSms(array $messages)
    {
        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/sms/send';

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->post($_queryUrl, $messages);

        $this->validateResponse($response->object());

        return $response->object();
    }
}
