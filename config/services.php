<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sqs' => [
        'url'    => env('SQS_URL'),
        'key'    => env('SQS_KEY'),
        'secret' => env('SQS_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model'  => App\Models\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'rollbar' => [
        'access_token' => (isset( $_SERVER['HTTP_HOST']) && strpos( $_SERVER['HTTP_HOST'], 'amazonaws.com') !== false) ? '' : env('ROLLBAR_TOKEN'),
        'level'        => env('ROLLBAR_LEVEL', 'error'),
        'batched'      => false,
        'person'       => 'user_array',
        'scrub_fields' => ['passwd', 'password', 'secret', 'confirm_password', 'password_confirmation', 'auth_token', 'csrf_token', 'card']
    ],

    'twitter' => [
        'client_id'     => env('TWITTER_CONSUMER_KEY'),
        'client_secret' => env('TWITTER_CONSUMER_SECRET'),
        'redirect'      => env('TWITTER_OAUTH_REDIRECT'),
    ],

    'instagram' => [
        'client_id'     => env('INSTAGRAM_API_KEY'),
        'client_secret' => env('INSTAGRAM_API_SECRET'),
        'redirect'      => env('INSTAGRAM_API_REDIRECT'),
    ],
];
