<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    /**
     * 获取设置值
     */
    public static function get($key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * 设置值
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );

        // 清除缓存
        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * 批量设置
     */
    public static function setMany(array $settings)
    {
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                self::set($key, json_encode($value), 'json');
            } else {
                self::set($key, $value);
            }
        }
    }

    /**
     * 获取所有设置
     */
    public static function getAll()
    {
        return Cache::remember('all_settings', 3600, function () {
            $settings = self::all();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * 获取分组设置
     */
    public static function getGroup($group)
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            $settings = self::where('group', $group)->get();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * 清除所有缓存
     */
    public static function clearCache()
    {
        Cache::forget('all_settings');
        Cache::forget('settings_group_general');
        Cache::forget('settings_group_cos');
        Cache::forget('settings_group_file');
        Cache::forget('settings_group_system');
    }

    /**
     * 类型转换
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'boolean':
                return (bool) $value;
            case 'json':
                return json_decode($value, true);
            case 'float':
                return (float) $value;
            default:
                return $value;
        }
    }

    /**
     * 初始化默认设置
     */
    public static function initializeDefaults()
    {
        $defaults = [
            // 基本设置
            'site_name' => ['value' => '视频管理系统', 'type' => 'string', 'group' => 'general', 'description' => '网站名称'],
            'admin_email' => ['value' => 'admin@example.com', 'type' => 'string', 'group' => 'general', 'description' => '管理员邮箱'],
            
            // 文件设置
            'max_file_size' => ['value' => 100, 'type' => 'integer', 'group' => 'file', 'description' => '最大文件大小(MB)'],
            'allowed_file_types' => ['value' => 'mp4,mov,avi', 'type' => 'string', 'group' => 'file', 'description' => '允许的文件类型'],
            
            // 系统设置
            'language' => ['value' => 'zh', 'type' => 'string', 'group' => 'system', 'description' => '系统语言'],
            
            // 腾讯云 COS 设置
            'use_cos' => ['value' => false, 'type' => 'boolean', 'group' => 'cos', 'description' => '是否启用腾讯云 COS'],
            'cos_secret_id' => ['value' => '', 'type' => 'string', 'group' => 'cos', 'description' => '腾讯云 Secret ID'],
            'cos_secret_key' => ['value' => '', 'type' => 'string', 'group' => 'cos', 'description' => '腾讯云 Secret Key'],
            'cos_region' => ['value' => 'ap-beijing', 'type' => 'string', 'group' => 'cos', 'description' => '腾讯云 COS 地域'],
            'cos_bucket' => ['value' => '', 'type' => 'string', 'group' => 'cos', 'description' => '腾讯云 COS 存储桶'],
            'cos_domain' => ['value' => '', 'type' => 'string', 'group' => 'cos', 'description' => '腾讯云 COS 自定义域名'],
            'cos_timeout' => ['value' => 60, 'type' => 'integer', 'group' => 'cos', 'description' => '腾讯云 COS 超时时间'],
        ];

        foreach ($defaults as $key => $config) {
            self::updateOrCreate(
                ['key' => $key],
                $config
            );
        }

        self::clearCache();
    }
}
