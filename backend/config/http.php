<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for HTTP client operations, including SSL settings
    |
    */

    'ssl' => [
        'verify_peer' => env('HTTP_SSL_VERIFY_PEER', false),
        'verify_host' => env('HTTP_SSL_VERIFY_HOST', false),
        'timeout' => env('HTTP_TIMEOUT', 30),
        'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 10),
    ],

    'gemini' => [
        'timeout' => env('GEMINI_TIMEOUT', 30),
        'max_retries' => env('GEMINI_MAX_RETRIES', 3),
        'retry_delay' => env('GEMINI_RETRY_DELAY', 1), // seconds
    ],
];
