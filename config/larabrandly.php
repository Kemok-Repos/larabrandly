<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rebrandly API Key
    |--------------------------------------------------------------------------
    |
    | Your Rebrandly API key. You can find this in your Rebrandly dashboard
    | under API settings. Make sure to add this to your .env file.
    |
    */
    'api_key' => env('REBRANDLY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Domain
    |--------------------------------------------------------------------------
    |
    | The default domain to use for short links when none is specified.
    | This should be one of your configured domains in Rebrandly.
    |
    */
    'default_domain' => env('REBRANDLY_DEFAULT_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the HTTP client used to communicate
    | with the Rebrandly API.
    |
    */
    'http' => [
        'timeout' => env('REBRANDLY_HTTP_TIMEOUT', 30),
        'retry_times' => env('REBRANDLY_RETRY_TIMES', 3),
        'retry_delay' => env('REBRANDLY_RETRY_DELAY', 100), // milliseconds
    ],
];