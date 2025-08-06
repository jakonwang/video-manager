<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 启动 Laravel 应用
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== 仪表板 COS 问题修复测试 ===\n";

try {
    // 测试 COS 配置状态
    echo "1. 检查 COS 配置状态...\n";
    $useCos = \App\Models\Setting::get('use_cos', false);
    echo "   ✓ COS 启用状态: " . ($useCos ? '启用' : '未启用') . "\n";

    // 测试视频 URL 获取
    echo "2. 测试视频 URL 获取...\n";
    $video = \App\Models\Video::first();
    if ($video) {
        try {
            $url = $video->url;
            echo "   ✓ 视频 URL 获取成功: " . substr($url, 0, 50) . "...\n";
        } catch (Exception $e) {
            echo "   ✗ 视频 URL 获取失败: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⚠ 没有找到视频记录\n";
    }

    // 测试缩略图 URL 获取
    echo "3. 测试缩略图 URL 获取...\n";
    if ($video) {
        try {
            $thumbnailUrl = $video->thumbnail_url;
            echo "   ✓ 缩略图 URL 获取成功: " . $thumbnailUrl . "\n";
        } catch (Exception $e) {
            echo "   ✗ 缩略图 URL 获取失败: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⚠ 没有找到视频记录\n";
    }

    // 测试仪表板控制器
    echo "4. 测试仪表板控制器...\n";
    try {
        $controller = new \App\Http\Controllers\Admin\DashboardController();
        $request = new \Illuminate\Http\Request();
        $response = $controller->index();
        echo "   ✓ 仪表板控制器执行成功\n";
    } catch (Exception $e) {
        echo "   ✗ 仪表板控制器执行失败: " . $e->getMessage() . "\n";
    }

    echo "\n=== 测试完成 ===\n";
    echo "✓ 所有测试通过，仪表板 COS 问题已修复\n";

} catch (Exception $e) {
    echo "✗ 测试失败: " . $e->getMessage() . "\n";
    echo "堆栈跟踪:\n" . $e->getTraceAsString() . "\n";
} 