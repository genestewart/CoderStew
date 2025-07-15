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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Microsoft Services
    |--------------------------------------------------------------------------
    |
    | Configuration for Microsoft Graph API and Bookings integration.
    |
    */

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'redirect_uri' => env('MICROSOFT_REDIRECT_URI'),
        'bookings_business_id' => env('MICROSOFT_BOOKINGS_BUSINESS_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Listmonk Newsletter Service
    |--------------------------------------------------------------------------
    |
    | Configuration for Listmonk newsletter management system.
    |
    */

    'listmonk' => [
        'url' => env('LISTMONK_URL', 'http://listmonk:9000'),
        'username' => env('LISTMONK_USERNAME'),
        'password' => env('LISTMONK_PASSWORD'),
        'default_list_id' => env('LISTMONK_DEFAULT_LIST_ID', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Service
    |--------------------------------------------------------------------------
    |
    | Configuration for Google reCAPTCHA spam protection.
    |
    */

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'version' => env('RECAPTCHA_VERSION', 'v3'),
    ],

    /*
    |--------------------------------------------------------------------------
    | GlitchTip Error Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for GlitchTip error tracking and monitoring.
    |
    */

    'glitchtip' => [
        'dsn' => env('GLITCHTIP_DSN'),
        'environment' => env('APP_ENV', 'production'),
        'release' => env('APP_VERSION', '1.0.0'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for performance monitoring and metrics collection.
    |
    */

    'performance' => [
        'enabled' => env('PERFORMANCE_MONITORING', true),
        'sample_rate' => env('PERFORMANCE_SAMPLE_RATE', 0.1),
        'max_response_time' => env('PERFORMANCE_MAX_RESPONSE_TIME', 2000), // milliseconds
        'max_ttfb' => env('PERFORMANCE_MAX_TTFB', 200), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage Services
    |--------------------------------------------------------------------------
    |
    | Configuration for file storage and CDN services.
    |
    */

    'storage' => [
        'cdn_url' => env('CDN_URL'),
        'image_optimization' => env('IMAGE_OPTIMIZATION', true),
        'max_file_size' => env('MAX_FILE_SIZE', 10485760), // 10MB in bytes
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for social media platform integrations.
    |
    */

    'social' => [
        'twitter' => [
            'api_key' => env('TWITTER_API_KEY'),
            'api_secret' => env('TWITTER_API_SECRET'),
            'access_token' => env('TWITTER_ACCESS_TOKEN'),
            'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
        ],
        'linkedin' => [
            'client_id' => env('LINKEDIN_CLIENT_ID'),
            'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        ],
        'github' => [
            'token' => env('GITHUB_TOKEN'),
            'username' => env('GITHUB_USERNAME'),
        ],
    ],

];
