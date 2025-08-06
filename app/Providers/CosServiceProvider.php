<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Qcloud\Cos\Client;
use Illuminate\Support\Facades\Storage;

class CosServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 注册 COS 客户端为单例
        $this->app->singleton('cos.client', function ($app) {
            try {
                // 检查是否启用 COS
                $useCos = Setting::get('use_cos', false);
                
                if (!$useCos) {
                    Log::info('COS 未启用，跳过客户端创建');
                    return null;
                }

                // 获取 COS 配置
                $secretId = Setting::get('cos_secret_id', '');
                $secretKey = Setting::get('cos_secret_key', '');
                $region = Setting::get('cos_region', 'ap-beijing');
                $timeout = Setting::get('cos_timeout', 60);

                // 检查必要的配置
                if (empty($secretId) || empty($secretKey)) {
                    Log::warning('COS 配置不完整，跳过客户端创建');
                    return null;
                }

                $cosConfig = [
                    'region' => $region,
                    'credentials' => [
                        'secretId' => $secretId,
                        'secretKey' => $secretKey,
                    ],
                    'timeout' => $timeout,
                    'connect_timeout' => 10,
                ];

                $client = new Client($cosConfig);
                Log::info('COS 客户端创建成功');
                return $client;
                
            } catch (\Exception $e) {
                Log::error('COS 客户端创建失败', ['error' => $e->getMessage()]);
                return null;
            }
        });

        // 注册 COS 适配器
        $this->app->singleton('cos.adapter', function ($app) {
            try {
                $client = $app->make('cos.client');
                
                if (!$client) {
                    Log::info('COS 客户端不可用，跳过适配器创建');
                    return null;
                }

                $bucket = Setting::get('cos_bucket', '');
                $region = Setting::get('cos_region', 'ap-beijing');
                $domain = Setting::get('cos_domain', '');

                if (empty($bucket)) {
                    Log::warning('COS 存储桶配置缺失，跳过适配器创建');
                    return null;
                }

                $adapterConfig = [
                    'bucket' => $bucket,
                    'region' => $region,
                    'domain' => $domain,
                ];

                $adapter = new \App\Services\CosAdapter($client, $adapterConfig);
                Log::info('COS 适配器创建成功');
                return $adapter;
                
            } catch (\Exception $e) {
                Log::error('COS 适配器创建失败', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            Storage::extend('cos', function ($app, $config) {
                try {
                    // 检查是否启用 COS
                    $useCos = Setting::get('use_cos', false);
                    
                    if (!$useCos) {
                        Log::info('COS 未启用，使用本地存储');
                        // 返回本地存储适配器
                        return $app->make('filesystem.disk', ['local']);
                    }

                    $cosAdapter = $app->make('cos.adapter');
                    
                    if (!$cosAdapter) {
                        Log::warning('COS 适配器不可用，使用本地存储');
                        // 返回本地存储适配器
                        return $app->make('filesystem.disk', ['local']);
                    }

                    Log::info('COS 存储扩展创建成功');
                    return $cosAdapter;
                    
                } catch (\Exception $e) {
                    Log::error('COS 存储扩展创建失败，使用本地存储', ['error' => $e->getMessage()]);
                    // 返回本地存储适配器
                    return $app->make('filesystem.disk', ['local']);
                }
            });
            
            Log::info('COS 服务提供者启动成功');
            
        } catch (\Exception $e) {
            Log::error('COS 服务提供者启动失败', ['error' => $e->getMessage()]);
        }
    }
} 