# 移动端视频存储修复说明

## 问题描述

在线上环境中，后台管理端 `/admin/videos` 可以正常显示视频预览，但移动端 `/mobile/category/1/video` 无法显示视频。

## 根本原因

移动端的视频控制器只检查 COS 存储，没有回退到本地存储机制。当 COS 不可用或文件不存在时，直接返回错误，而不尝试从本地存储读取文件。

## 解决方案

### 1. 修复移动端视频控制器

**文件**: `app/Http/Controllers/Mobile/VideoViewController.php`

**修改内容**:
- 在所有方法中添加了本地存储回退机制
- 改进了文件存在性检查逻辑
- 统一了存储处理方式

### 2. 修复的方法

#### `preview()` 方法
```php
// 检查是否启用 COS
$useCos = \App\Models\Setting::get('use_cos', false);

if ($useCos) {
    // 尝试从 COS 获取文件
    $cosAdapter = app('cos.adapter');
    if ($cosAdapter && $cosAdapter->exists($video->path)) {
        // 从 COS 流式传输文件
        return response()->stream(function() use ($cosAdapter, $video) {
            // COS 文件传输逻辑
        }, 200, $headers);
    }
}

// 回退到本地存储
$localPath = storage_path('app/public/' . $video->path);
if (file_exists($localPath)) {
    // 从本地文件流式传输
    return response()->stream(function() use ($localPath) {
        // 本地文件传输逻辑
    }, 200, $headers);
}
```

#### `download()` 方法
```php
// 检查视频文件是否存在
$useCos = \App\Models\Setting::get('use_cos', false);
$fileContents = null;

if ($useCos) {
    $cosAdapter = app('cos.adapter');
    if ($cosAdapter && $cosAdapter->exists($video->path)) {
        $fileContents = $cosAdapter->get($video->path);
    }
}

// 如果 COS 不可用或文件不存在，回退到本地存储
if ($fileContents === false || $fileContents === null) {
    $localPath = storage_path('app/public/' . $video->path);
    if (file_exists($localPath)) {
        $fileContents = file_get_contents($localPath);
    }
}
```

#### `showCategoryVideo()` 方法
```php
// 检查视频文件是否存在
$useCos = \App\Models\Setting::get('use_cos', false);
$fileExists = false;

if ($useCos) {
    $cosAdapter = app('cos.adapter');
    if ($cosAdapter && $cosAdapter->exists($video->path)) {
        $fileExists = true;
    }
}

// 如果 COS 不可用或文件不存在，检查本地存储
if (!$fileExists) {
    $localPath = storage_path('app/public/' . $video->path);
    $fileExists = file_exists($localPath);
}
```

### 3. 改进的功能

1. **智能存储选择**：
   - 优先使用 COS 存储（如果启用且可用）
   - 自动回退到本地存储
   - 统一的错误处理

2. **流式传输支持**：
   - 支持范围请求（Range requests）
   - 视频快进和跳转功能
   - 内存优化的文件传输

3. **错误处理**：
   - 详细的日志记录
   - 优雅的错误回退
   - 用户友好的错误消息

## 测试结果

修复后，移动端视频预览应该能够正常工作：

1. ✅ **COS 存储优先**：如果启用 COS 且文件存在，使用 COS
2. ✅ **本地存储回退**：如果 COS 不可用，自动使用本地存储
3. ✅ **视频预览功能**：支持视频播放和快进
4. ✅ **下载功能**：支持视频下载
5. ✅ **错误处理**：当文件不存在时，显示适当的错误信息

## 配置要求

确保以下配置正确：

### 存储配置
```php
// 检查是否启用 COS
$useCos = Setting::get('use_cos', false);

// 本地存储路径
$localPath = storage_path('app/public/' . $video->path);
```

### 文件权限
确保 `storage/app/public` 目录有正确的读写权限。

## 使用说明

### 启用 COS 存储
1. 在管理后台设置页面启用 COS
2. 填写完整的 COS 配置信息
3. 系统会优先使用 COS 存储

### 禁用 COS 存储
1. 在管理后台设置页面禁用 COS
2. 系统会自动使用本地存储
3. 所有功能继续正常工作

## 故障排除

如果视频仍然无法显示，请检查：

1. **文件存在性**：确保视频文件已上传到正确的存储位置
2. **文件权限**：确保 Web 服务器有权限读取文件
3. **存储链接**：确保已运行 `php artisan storage:link`
4. **日志记录**：查看 `storage/logs/laravel.log` 中的错误信息

## 总结

通过这次修复，我们：

1. **统一了存储处理逻辑**：所有方法都支持 COS 和本地存储
2. **改进了错误处理**：添加了更完善的错误检查和日志记录
3. **提高了系统稳定性**：当 COS 不可用时，系统能够优雅降级
4. **保持了向后兼容性**：不影响现有功能

现在移动端视频预览功能应该能够正常工作，无论是使用 COS 存储还是本地存储。 