<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ClickSend Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may specify your ClickSend username and API key.
    |
    */

    'username' => env('CLICKSEND_USERNAME'),
    'api_key' => env('CLICKSEND_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | White List
    |--------------------------------------------------------------------------
    |
    | Set this option to true if you want to enable a white list for phone
    | numbers. When enabled, messages will only be sent to the phone numbers
    | listed in the `whitelist` array below.
    |
    */

    'whitelist_enabled' => env('CLICKSEND_WHITELIST_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Environment-specific white lists
    |--------------------------------------------------------------------------
    |
    | This section allows you to define environment-specific white lists.
    | Each environment can have its own set of phone numbers that are allowed
    | to receive messages. If the current environment matches one of the keys
    | below, the corresponding white list will be used.
    |
    */

    'whitelists' => [

        'local' => [
            '+61411111111',
        ],

        'staging' => [
            '+61422222222',
        ],

    ],

];
