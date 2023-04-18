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
    | White List testing number
    |--------------------------------------------------------------------------
    |
    | Set this option will replace any numbers not in a specified whitelist
    | with the ClickSend testing number. This will still record and return
    | a response in the ClickSend dashboard but will not incur any charges
    |
    */

    'whitelist_testing_number' => env('CLICKSEND_WHITELIST_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Environment-specific white lists
    |--------------------------------------------------------------------------
    |
    | This comma separated list allows a definition environment-specific whitelists.
    | Each environment can have its own set of phone numbers that will allow delivery.
    | An exception is thrown if whitelist_enabled is true but no matching environment is present
    |
    */

    'whitelists' => [

        'local' => env('CLICKSEND_LOCAL_WHITELIST_NUMBERS', '+61411111111'),

    ],

];
