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

    'msg91' => [
        'authkey'     => env('MSG91_AUTHKEY'),
        'sender'      => env('MSG91_SENDER'),
        'route'       => env('MSG91_ROUTE'),
        'dlt_te_id'   => env('MSG91_DLT_TEMPLATE_ID'),
        'otp_length'  => env('OTP_LENGTH', 6),
        'country'     => env('MSG91_COUNTRY_CODE', '91'),
        'endpoint'  =>   env('MSG91_ENDPOINT'),
    ],
    
    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'tenant_id' => env('AZURE_TENANT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'sender' => 'noreply@active.cisce.org',
    ],
    
    'acs' => [
        'endpoint' => env('AZURE_MAIL_ENDPOINT'),
        'access_key' => env('AZURE_MAIL_KEY'),
        'sender_address' => env('MAIL_FROM_ADDRESS'),
        'mail_from_name'  => env('MAIL_FROM_NAME'),
        'mail_resource_name'  => env('AZURE_MAIL_RESOURCE_NAME'),
    ],

    'careers' => [
        'url'   => env('CAREERS_API_URL'),
        'token' => env('CAREERS_API_TOKEN'),
    ],

    'email_api' => [
        'url'   => env('EMAIL_API_URL'),
        'token' => env('EMAIL_API_TOKEN'),
    ],
];
