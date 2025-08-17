<?php

declare(strict_types=1);

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

    'street' => [
        'failed_jobs_email' => env('STREET_FAILED_JOBS_EMAIL', 'you@example.com'),

        // Open API
        'open' => [
            'url' => env('STREET_OPEN_API_URL'),
            'token' => env('STREET_OPEN_API_TOKEN'),
        ],

        // Property Feed API
        'feed' => [
            'url' => env('STREET_FEED_API_URL'),
            'token' => env('STREET_FEED_API_TOKEN'),
            'prune_media' => (bool) env('STREET_FEED_PRUNE_MEDIA', false),

            'sales' => [
                'endpoint' => '/sales/search',
                'per_page' => (int) env('STREET_SALES_PER_PAGE', 200),
                'includes' => env('STREET_SALES_INCLUDES', 'images,floorplans,epc'),
                'max_results' => env('STREET_SALES_MAX_RESULTS'),
            ],

            'lettings' => [
                'endpoint' => '/lettings/search',
                'per_page' => (int) env('STREET_LETTINGS_PER_PAGE', 200),
                'includes' => env('STREET_LETTINGS_INCLUDES', 'images,floorplans,epc'),
                'max_results' => env('STREET_LETTINGS_MAX_RESULTS'),
            ],
        ],
    ],
];
