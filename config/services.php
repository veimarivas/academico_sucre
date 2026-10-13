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

    'seguimiento_esam' => [
        'url' => env('SEGUIMIENTO_ESAM_URL', 'http://127.0.0.1:8020'),
        // Base del panel web (donde vive /sso y el resto de la UI). Puede ser
        // distinto del host/puerto de la API de integración.
        'panel_url' => env('SEGUIMIENTO_ESAM_PANEL_URL', 'http://127.0.0.1:8030'),
        'token' => env('SEGUIMIENTO_ESAM_TOKEN'),
        'enabled' => env('SEGUIMIENTO_ESAM_ENABLED', true),
        'timeout' => (int) env('SEGUIMIENTO_ESAM_TIMEOUT', 15),
        'sso_secret' => env('SEGUIMIENTO_ESAM_SSO_SECRET'),
    ],

];
