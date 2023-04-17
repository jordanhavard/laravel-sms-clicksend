<?php

namespace JordanHavard\ClickSend\Controllers;

use ClickSendLib\APIHelper;
use ClickSendLib\Configuration;
use Illuminate\Support\Facades\Http;
use JordanHavard\ClickSend\Exceptions\APIException;
use JordanHavard\ClickSend\Models\SmsMessage;

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
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @todo Add general description for this endpoint
     *
     * @param  SmsMessage  $messages TODO: type description here
     * @return string response from the API call
     *
     * @throws APIException|\Unirest\Exception Thrown if API call fails
     */
    public function sendSms(
        $messages
    ) {
        //check that all required arguments are provided
        if (! isset($messages)) {
            throw new \InvalidArgumentException('One or more required arguments were NULL.');
        }

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
