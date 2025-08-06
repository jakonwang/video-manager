<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        // 腾讯云 COS 配置
        'cos' => [
            'driver' => 'cos',
            'app_id' => env('COS_APP_ID'),
            'secret_id' => env('COS_SECRET_ID'),
            'secret_key' => env('COS_SECRET_KEY'),
            'region' => env('COS_REGION', 'ap-beijing'),
            'bucket' => env('COS_BUCKET'),
            'domain' => env('COS_DOMAIN'),
            'scheme' => env('COS_SCHEME', 'https'),
            'timeout' => env('COS_TIMEOUT', 60),
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];