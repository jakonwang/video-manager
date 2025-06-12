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
        ]);

        $settings = $this->getSettings();

        // 更新设置
        $settings = array_merge($settings, $request->only([
            'site_name',
            'admin_email',
            'max_file_size',
            'allowed_file_types',
            'language',
        ]));

        // 保存设置
        Storage::put($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('admin.settings')
            ->with('success', '设置已更新！');
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
        ];
    }
}