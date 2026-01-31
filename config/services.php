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

    'whatsapp' => [
        // Provider selection: 'meta', 'local', 'evolution', 'wasender', or 'auto' (tries wasender first, falls back to evolution)
        'provider' => env('WHATSAPP_PROVIDER', 'wasender'),
        
        // Meta WhatsApp Cloud API configuration
        'meta' => [
            'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
            'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
            'api_version' => 'v21.0',
        ],
        
        // Local go-whatsapp-web-multidevice API configuration
        'local' => [
            'api_url' => env('WHATSAPP_LOCAL_API_URL', 'http://localhost:3000'),
            'webhook_secret' => env('WHATSAPP_LOCAL_WEBHOOK_SECRET'),
        ],
        
        // Evolution API configuration (deprecated, use WasenderAPI instead)
        'evolution' => [
            'api_url' => env('EVOLUTION_API_URL', 'http://147.93.85.45:8080'),
            'api_key' => env('EVOLUTION_API_KEY', '429683C4C977415CAAFCCE10F7D57E11'),
            'instance_name' => env('EVOLUTION_INSTANCE_NAME', 'Mycosoft Technologies'),
        ],
        
        // WasenderAPI configuration
        'wasender' => [
            'api_url' => env('WASENDER_API_URL', 'https://wasenderapi.com/api'),
            'api_key' => env('WASENDER_API_KEY'),
            'sender' => env('WASENDER_SENDER'),
        ],
    ],

];
