<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Qcloud\Cos\Client;

class CosServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('cos', function ($app, $config) {
            $cosConfig = [
                'region' => $config['region'],
                'credentials' => [
                    'secretId' => $config['secret_id'],
                    'secretKey' => $config['secret_key'],
                ],
                'timeout' => $config['timeout'] ?? 60,
                'connect_timeout' => 10,
            ];

            $client = new Client($cosConfig);

            return new \App\Services\CosAdapter($client, $config);
        });
    }
} 