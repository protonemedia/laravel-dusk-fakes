<?php

return [
    'bus' => [
        'enabled' => env('DUSK_FAKE_BUS', false),
        'storage_root' => storage_path('framework/testing/bus'),
    ],

    'mails' => [
        'enabled' => env('DUSK_FAKE_MAILS', false),
        'storage_root' => storage_path('framework/testing/mails'),
    ],

    'notifications' => [
        'enabled' => env('DUSK_FAKE_NOTIFICATIONS', false),
        'storage_root' => storage_path('framework/testing/notifications'),
    ],

    'queue' => [
        'enabled' => env('DUSK_FAKE_QUEUE', false),
        'storage_root' => storage_path('framework/testing/queue'),
    ],
];
