<?php

if (!function_exists('formatBytes')) {
    /**
     * 格式化文件大小
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('getSiteName')) {
    /**
     * 获取站点名称
     *
     * @return string
     */
    function getSiteName()
    {
        return config('app.name', '视频管理系统');
    }
}

if (!function_exists('getLocaleDisplayName')) {
    /**
     * 获取语言显示名称
     *
     * @param string $locale
     * @return string
     */
    function getLocaleDisplayName($locale)
    {
        $names = [
            'zh' => '中文',
            'en' => 'English',
            'vi' => 'Tiếng Việt'
        ];

        return $names[$locale] ?? $locale;
    }
} 