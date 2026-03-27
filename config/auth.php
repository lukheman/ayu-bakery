<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'admin_toko'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'admin_toko'),
    ],

    'guards' => [
        'admin_toko' => [
            'driver' => 'session',
            'provider' => 'admin_toko',
        ],
        'pemilik_toko' => [
            'driver' => 'session',
            'provider' => 'pemilik_toko',
        ],
        'kasir' => [
            'driver' => 'session',
            'provider' => 'kasir',
        ],
        'reseller' => [
            'driver' => 'session',
            'provider' => 'reseller',
        ],
        'kurir' => [
            'driver' => 'session',
            'provider' => 'kurir',
        ],
    ],

    'providers' => [
        'admin_toko' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminToko::class,
        ],
        'pemilik_toko' => [
            'driver' => 'eloquent',
            'model' => App\Models\PemilikToko::class,
        ],
        'kasir' => [
            'driver' => 'eloquent',
            'model' => App\Models\Kasir::class,
        ],
        'reseller' => [
            'driver' => 'eloquent',
            'model' => App\Models\Reseller::class,
        ],
        'kurir' => [
            'driver' => 'eloquent',
            'model' => App\Models\Kurir::class,
        ],
    ],

    'passwords' => [
        'admin_toko' => [
            'provider' => 'admin_toko',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
