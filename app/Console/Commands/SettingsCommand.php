<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:manage {action} {--key=} {--value=} {--group=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage application settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'init':
                $this->initializeSettings();
                break;
            case 'get':
                $this->getSetting();
                break;
            case 'set':
                $this->setSetting();
                break;
            case 'list':
                $this->listSettings();
                break;
            case 'clear-cache':
                $this->clearCache();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: init, get, set, list, clear-cache');
                return 1;
        }

        return 0;
    }

    /**
     * 初始化设置
     */
    protected function initializeSettings()
    {
        $this->info('Initializing default settings...');
        Setting::initializeDefaults();
        $this->info('Settings initialized successfully!');
    }

    /**
     * 获取设置
     */
    protected function getSetting()
    {
        $key = $this->option('key');
        
        if (!$key) {
            $this->error('Please provide a key using --key option');
            return;
        }

        $value = Setting::get($key);
        $this->info("Setting '{$key}': " . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
    }

    /**
     * 设置值
     */
    protected function setSetting()
    {
        $key = $this->option('key');
        $value = $this->option('value');
        $group = $this->option('group') ?: 'general';

        if (!$key || !$value) {
            $this->error('Please provide both --key and --value options');
            return;
        }

        Setting::set($key, $value, 'string', $group);
        $this->info("Setting '{$key}' updated successfully!");
    }

    /**
     * 列出所有设置
     */
    protected function listSettings()
    {
        $settings = Setting::all();
        
        if ($settings->isEmpty()) {
            $this->info('No settings found.');
            return;
        }

        $this->table(
            ['Key', 'Value', 'Type', 'Group', 'Description'],
            $settings->map(function ($setting) {
                return [
                    $setting->key,
                    $setting->value,
                    $setting->type,
                    $setting->group,
                    $setting->description
                ];
            })
        );
    }

    /**
     * 清除缓存
     */
    protected function clearCache()
    {
        Setting::clearCache();
        $this->info('Settings cache cleared successfully!');
    }
}
