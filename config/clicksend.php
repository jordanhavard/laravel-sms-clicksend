<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ClickSend Credentials
    |--------------------------------------------------------------------------
    |
    | Here you can specify your ClickSend username and API key.
    |
    */

    'username' => env('CLICKSEND_USERNAME'),
    'api_key' => env('CLICKSEND_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Whitelist
    |--------------------------------------------------------------------------
    |
    | Set this option to true if you want to enable a whitelist for sms message
    | sending. When enabled, messages will only be sent to the phone numbers
    | listed in the `whitelist` array below and any numbers that are not included
    | will be sent to the `whitelist_testing_number`
    |
    */

    'whitelist_enabled' => env('CLICKSEND_WHITELIST_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Whitelist Testing Number
    |--------------------------------------------------------------------------
    |
    | This is the number that will be substituted when the whitelist is enabled
    | and the `to` number is not defined inside the allowed whitelist for the
    | environment.
    |
    | See https://developers.clicksend.com/docs/rest/v3/#test-sms-mms-numbers
    |
    */

    'whitelist_testing_number' => env('CLICKSEND_WHITELIST_TESTING_NUMBER', '+61411111111'),

    /*
    |--------------------------------------------------------------------------
    | Whitelist api_response prepend
    |--------------------------------------------------------------------------
    |
    | This is what will be prepended to the api_response body for the particular message
    | when the whitelist is enabled.
    |
    */

    'whitelist_allowed' => env(
        'CLICKSEND_WHITELIST_ALLOWED',
        'WHITELIST_ALLOWED: '
    ),

    'whitelist_prevented' => env(
        'CLICKSEND_WHITELIST_PREVENTED',
        'WHITELIST_PREVENTED: '
    ),

    /*
    |--------------------------------------------------------------------------
    | Environment-specific whitelists
    |--------------------------------------------------------------------------
    |
    | This comma separated list allows an environment-specific defined whitelist.
    | Each environment can have its own set of phone numbers that will allow delivery.
    | Numbers not included in this list will instead send a message to `whitelist_testing_number`
    |
    */

    'whitelists' => [

        'local' => env('CLICKSEND_LOCAL_WHITELIST_NUMBERS', '+61411111111'),

    ],

];
