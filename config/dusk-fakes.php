<?php

return [
    'notifications' => [
        'enabled' => env('DUSK_FAKE_NOTIFICATIONS', false),
        'storage_root' => storage_path('framework/testing/notifications'),
    ],
];
