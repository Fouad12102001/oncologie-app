<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [

        // Guard Web standard Laravel
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // ========================
        // Guard Oncologie
        // ========================
        'oncologie' => [
            'driver'   => 'session',
            'provider' => 'onco_users',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        // Provider standard Laravel
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        // ========================
        // Provider Oncologie
        // ========================
        'onco_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Oncologie\OncoUser::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'onco_users' => [
            'provider' => 'onco_users',
            'table'    => 'onco_password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

];