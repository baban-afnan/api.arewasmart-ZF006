<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'deepseek' => [
        'key' => env('DEEPSEEK_API_KEY'),
        'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
    ],

    'vtpass' => [
        'api_key'       => env('API_KEY'),
        'secret_key'    => env('SECRET_KEY'),
        'base_url'      => env('VTPASS_BASE_URL', 'https://sandbox.vtpass.com/api'),
        'variation_url' => env('VARIATION_URL', 'https://sandbox.vtpass.com/api/service-variations?serviceID='),
        'payment_url'   => env('MAKE_PAYMENT', 'https://sandbox.vtpass.com/api/pay'),
    ],

    'datastation' => [
        'token' => env('AUTH_TOKEN'),
        'epin_endpoint' => env('DATASTATION_EPIN_ENDPOINT', 'https://datastationapi.com/api/epin/'),
    ],

];
