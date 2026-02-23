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
    | LLM Backend (Groq cloud or LM Studio local)
    | LLM_DRIVER=groq  → pakai Groq cloud API
    | LLM_DRIVER=local → pakai LM Studio di localhost
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

    'lmstudio' => [
        'base_url' => env('LMSTUDIO_URL', 'http://127.0.0.1:1234'),
        'model'    => env('LMSTUDIO_MODEL', 'google/gemma-3-4b'),
    ],

    'kimi' => [
        'api_key' => env('KIMI_API_KEY'),
        'model'   => env('KIMI_MODEL', 'kimi-k2-turbo-preview'),
    ],

    'nvidia' => [
        'api_key' => env('NVIDIA_API_KEY'),
        'model'   => env('NVIDIA_MODEL', 'mistralai/mistral-large-3-675b-instruct-2512'),
    ],

    'llm' => [
        'driver' => env('LLM_DRIVER', 'nvidia'), // 'groq' | 'local' | 'kimi' | 'nvidia'
    ],

];
