<?php

namespace JordanHavard\ClickSend\Controllers;

use ClickSendLib\APIHelper;
use ClickSendLib\Configuration;
use Illuminate\Support\Facades\Http;
use JordanHavard\ClickSend\ClickSendSubaccount;
use JordanHavard\ClickSend\Exceptions\APIException;

class SubaccountController extends BaseController
{
    /**
     * @var \ClickSendLib\Controllers\SubaccountController The reference to *Singleton* instance of this class
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
    public function create(ClickSendSubaccount $properties)
    {
        if (! $properties->ready()) {
            return false;
        }
        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/subaccounts';

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->post($_queryUrl, (array) $properties);

        $this->validateResponse($response->object());

        return $response->object();
    }

    public function update(int $subaccount_id, ClickSendSubaccount $properties)
    {
        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/subaccounts/'.$subaccount_id;

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->put($_queryUrl, (array) $properties);

        $this->validateResponse($response->object());

        return $response->object();
    }

    public function delete(int $subaccount_id)
    {
        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/subaccounts/'.$subaccount_id;

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->delete($_queryUrl);

        $this->validateResponse($response->object());

        return $response->object();
    }

    public function index(int $page = 1, int $limit = 15)
    {

        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/subaccounts?page='.$page.'&limit='.$limit;

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->get($_queryUrl);

        $this->validateResponse($response->object());

        return $response->object();
    }

    public function view(int $subaccountId)
    {

        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/subaccounts/'.$subaccountId;

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->get($_queryUrl);

        $this->validateResponse($response->object());

        return $response->object();
    }

    public function generate_new_api_key(int $subaccountId)
    {
        //the base uri for api requests
        $_queryBuilder = Configuration::$BASEURI;

        //prepare query string for API call
        $_queryBuilder = $_queryBuilder.'/subaccounts/'.$subaccountId.'/regen-api-key';

        //validate and preprocess url
        $_queryUrl = APIHelper::cleanUrl($_queryBuilder);

        //prepare headers
        $_headers = [
            'user-agent' => 'ClickSendSDK',
            'content-type' => 'application/json; charset=utf-8',
        ];

        $response = Http::withHeaders($_headers)
            ->withBasicAuth(Configuration::$username, Configuration::$key)
            ->put($_queryUrl);

        $this->validateResponse($response->object());

        return $response->object();
    }
}
