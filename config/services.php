<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS, Google AI, and more.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | NVIDIA NIM — https://integrate.api.nvidia.com
    |--------------------------------------------------------------------------
    */
    'nvidia' => [
        'api_key' => env('NVIDIA_API_KEY'),
        'model'   => env('NVIDIA_MODEL', 'openai/gpt-oss-120b'),
        'url'     => env('NVIDIA_URL', 'https://integrate.api.nvidia.com/v1/chat/completions'),
    ],

];
