<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'brevo_newsletter' => [
        'api_key' => env('BREVO_NEWSLETTER_API_KEY'),
        'list_id' => env('BREVO_NEWSLETTER_LIST_ID'),
        'webhook_token' => env('BREVO_NEWSLETTER_WEBHOOK_TOKEN'),
        'base_url' => env('BREVO_NEWSLETTER_API_URL', 'https://api.brevo.com/v3'),
        'sender_email' => env('BREVO_NEWSLETTER_SENDER_EMAIL', env('BREVO_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'))),
        'sender_name' => env('BREVO_NEWSLETTER_SENDER_NAME', env('BREVO_MAIL_FROM_NAME', env('MAIL_FROM_NAME', env('APP_NAME', 'Digital Magazine')))),
    ],

];
