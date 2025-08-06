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

echo "=== 视频 URL 测试 ===\n";

try {
    // 获取第一个视频
    $video = \App\Models\Video::first();
    
    if ($video) {
        echo "视频ID: " . $video->id . "\n";
        echo "视频标题: " . $video->title . "\n";
        echo "视频路径: " . $video->path . "\n";
        echo "视频URL: " . $video->url . "\n";
        echo "视频大小: " . $video->formatted_size . "\n";
        echo "处理状态: " . ($video->processed ? '已处理' : '未处理') . "\n";
        
        // 检查文件是否存在
        if ($video->path) {
            $localPath = storage_path('app/public/' . $video->path);
            echo "本地文件路径: " . $localPath . "\n";
            echo "本地文件存在: " . (file_exists($localPath) ? '是' : '否') . "\n";
            
            if (file_exists($localPath)) {
                echo "文件大小: " . filesize($localPath) . " 字节\n";
            }
        }
    } else {
        echo "没有找到视频记录\n";
    }

} catch (Exception $e) {
    echo "测试失败: " . $e->getMessage() . "\n";
    echo "堆栈跟踪:\n" . $e->getTraceAsString() . "\n";
} 