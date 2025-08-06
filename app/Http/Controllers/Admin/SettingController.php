<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected $settingsFile = 'settings.json';

    /**
     * 显示设置页面
     */
    public function index()
    {
        $settings = $this->getSettings();
        return view('admin.settings', compact('settings'));
    }

    /**
     * 更新设置
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email|max:255',
            'max_file_size' => 'nullable|integer|min:1|max:10000',
            'allowed_file_types' => 'nullable|string|max:255',
            'language' => 'required|in:zh,en,vi',
            // 腾讯云 COS 配置验证
            'cos_secret_id' => 'nullable|string|max:255',
            'cos_secret_key' => 'nullable|string|max:255',
            'cos_region' => 'nullable|string|max:50',
            'cos_bucket' => 'nullable|string|max:255',
            'cos_domain' => 'nullable|url|max:255',
            'cos_timeout' => 'nullable|integer|min:10|max:600',
            'use_cos' => 'nullable|boolean',
        ]);

        $settings = $this->getSettings();

        // 更新基本设置
        $settings = array_merge($settings, $request->only([
            'site_name',
            'admin_email',
            'max_file_size',
            'allowed_file_types',
            'language',
        ]));

        // 更新腾讯云 COS 设置
        $cosSettings = [
            'use_cos' => $request->boolean('use_cos'),
            'cos_secret_id' => $request->cos_secret_id,
            'cos_secret_key' => $request->cos_secret_key,
            'cos_region' => $request->cos_region,
            'cos_bucket' => $request->cos_bucket,
            'cos_domain' => $request->cos_domain,
            'cos_timeout' => $request->cos_timeout ?? 60,
        ];

        $settings = array_merge($settings, $cosSettings);

        // 保存设置
        Storage::put($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        // 如果启用了 COS，更新环境变量
        if ($request->boolean('use_cos') && $request->cos_secret_id && $request->cos_secret_key) {
            $this->updateEnvironmentVariables($cosSettings);
        }

        return redirect()->route('admin.settings')
            ->with('success', '设置已更新！');
    }

    /**
     * 测试腾讯云 COS 连接
     */
    public function testCosConnection(Request $request)
    {
        $request->validate([
            'cos_secret_id' => 'required|string',
            'cos_secret_key' => 'required|string',
            'cos_region' => 'required|string',
            'cos_bucket' => 'required|string',
        ]);

        try {
            // 检查必要的 PHP 扩展
            $requiredExtensions = ['curl', 'json', 'xml', 'mbstring'];
            $missingExtensions = [];
            
            foreach ($requiredExtensions as $ext) {
                if (!extension_loaded($ext)) {
                    $missingExtensions[] = $ext;
                }
            }
            
            if (!empty($missingExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => '缺少必要的 PHP 扩展：' . implode(', ', $missingExtensions)
                ]);
            }

            // 检查网络连接 - 改进版本
            $testUrl = "https://cos.{$request->cos_region}.myqcloud.com";
            $ch = curl_init($testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // 只要能够连接到服务器就认为网络正常（不要求特定的状态码）
            if ($response === false || $curlError) {
                return response()->json([
                    'success' => false,
                    'message' => '网络连接测试失败，无法访问腾讯云 COS 服务。错误信息：' . $curlError . '。请检查网络连接或防火墙设置。'
                ]);
            }

            // 记录网络连接测试结果（用于调试）
            \Illuminate\Support\Facades\Log::info('COS 网络连接测试', [
                'url' => $testUrl,
                'http_code' => $httpCode,
                'response_length' => strlen($response),
                'curl_error' => $curlError
            ]);

            // 临时设置环境变量进行测试
            $this->setTemporaryEnv([
                'COS_SECRET_ID' => $request->cos_secret_id,
                'COS_SECRET_KEY' => $request->cos_secret_key,
                'COS_REGION' => $request->cos_region,
                'COS_BUCKET' => $request->cos_bucket,
            ]);

            // 尝试创建 COS 客户端
            try {
                $cosConfig = [
                    'region' => $request->cos_region,
                    'credentials' => [
                        'secretId' => $request->cos_secret_id,
                        'secretKey' => $request->cos_secret_key,
                    ],
                    'timeout' => 10,
                    'connect_timeout' => 5,
                ];

                $client = new \Qcloud\Cos\Client($cosConfig);
                
                // 测试存储桶访问权限
                try {
                    $result = $client->headBucket([
                        'Bucket' => $request->cos_bucket
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => '腾讯云 COS 连接测试成功！存储桶访问权限正常。'
                    ]);
                    
                } catch (\Exception $e) {
                    // 如果存储桶不存在，尝试列出存储桶
                    try {
                        $result = $client->listBuckets();
                        return response()->json([
                            'success' => true,
                            'message' => '腾讯云 COS 连接测试成功！但指定的存储桶不存在，请检查存储桶名称。'
                        ]);
                    } catch (\Exception $listException) {
                        return response()->json([
                            'success' => false,
                            'message' => '存储桶访问失败：' . $e->getMessage() . '。请检查存储桶名称和权限设置。'
                        ]);
                    }
                }
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('COS 客户端创建失败', [
                    'error' => $e->getMessage(),
                    'region' => $request->cos_region,
                    'has_secret_id' => !empty($request->cos_secret_id),
                    'has_secret_key' => !empty($request->cos_secret_key)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'COS 客户端创建失败：' . $e->getMessage() . '。请检查 Secret ID 和 Secret Key 是否正确。'
                ]);
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('COS 连接测试失败', [
                'error' => $e->getMessage(),
                'config' => $request->only(['cos_secret_id', 'cos_region', 'cos_bucket'])
            ]);

            return response()->json([
                'success' => false,
                'message' => '连接测试失败：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 更新环境变量
     */
    protected function updateEnvironmentVariables($cosSettings)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);
        
        // 更新或添加 COS 配置
        $envUpdates = [
            'FILESYSTEM_DISK' => 'cos',
            'COS_SECRET_ID' => $cosSettings['cos_secret_id'],
            'COS_SECRET_KEY' => $cosSettings['cos_secret_key'],
            'COS_REGION' => $cosSettings['cos_region'],
            'COS_BUCKET' => $cosSettings['cos_bucket'],
            'COS_DOMAIN' => $cosSettings['cos_domain'],
            'COS_TIMEOUT' => $cosSettings['cos_timeout'],
        ];

        foreach ($envUpdates as $key => $value) {
            if (empty($value)) continue;
            
            if (strpos($envContent, $key . '=') !== false) {
                // 更新现有配置
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // 添加新配置
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);
        
        // 清理配置缓存
        \Illuminate\Support\Facades\Artisan::call('config:clear');
    }

    /**
     * 临时设置环境变量
     */
    protected function setTemporaryEnv($variables)
    {
        foreach ($variables as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * 获取设置
     */
    protected function getSettings()
    {
        if (Storage::exists($this->settingsFile)) {
            return json_decode(Storage::get($this->settingsFile), true) ?? [];
        }

        // 默认设置
        return [
            'site_name' => '视频管理系统',
            'admin_email' => 'admin@example.com',
            'max_file_size' => 100,
            'allowed_file_types' => 'mp4,mov,avi',
            'language' => 'zh',
            'use_cos' => false,
            'cos_secret_id' => '',
            'cos_secret_key' => '',
            'cos_region' => 'ap-beijing',
            'cos_bucket' => '',
            'cos_domain' => '',
            'cos_timeout' => 60,
        ];
    }
}