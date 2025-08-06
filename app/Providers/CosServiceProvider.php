<?php

namespace App\Providers;

use App\Models\Setting;
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
            // 从数据库获取 COS 配置
            $useCos = Setting::get('use_cos', false);
            
            if (!$useCos) {
                throw new \Exception('腾讯云 COS 未启用，请在管理后台启用 COS 存储');
            }

            $cosConfig = [
                'region' => Setting::get('cos_region', 'ap-beijing'),
                'credentials' => [
                    'secretId' => Setting::get('cos_secret_id', ''),
                    'secretKey' => Setting::get('cos_secret_key', ''),
                ],
                'timeout' => Setting::get('cos_timeout', 60),
                'connect_timeout' => 10,
            ];

            // 检查必要的配置
            if (empty($cosConfig['credentials']['secretId']) || empty($cosConfig['credentials']['secretKey'])) {
                throw new \Exception('腾讯云 COS 配置不完整，请在管理后台配置 Secret ID 和 Secret Key');
            }

            $client = new Client($cosConfig);

            // 构建适配器配置
            $adapterConfig = [
                'bucket' => Setting::get('cos_bucket', ''),
                'region' => $cosConfig['region'],
                'domain' => Setting::get('cos_domain', ''),
            ];

            return new \App\Services\CosAdapter($client, $adapterConfig);
        });
    }
} 