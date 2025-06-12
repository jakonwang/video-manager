<?php

if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

if (!function_exists('getSiteName')) {
    function getSiteName() {
        $settingsFile = 'settings.json';
        if (\Illuminate\Support\Facades\Storage::exists($settingsFile)) {
            $settings = json_decode(\Illuminate\Support\Facades\Storage::get($settingsFile), true);
            return $settings['site_name'] ?? '视频管理系统';
        }
        return '视频管理系统';
    }
}