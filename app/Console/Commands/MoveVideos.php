<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MoveVideos extends Command
{
    protected $signature = 'videos:move';
    protected $description = '移动视频文件到正确的位置';

    public function handle()
    {
        $this->info('开始移动视频文件...');

        try {
            $videos = Video::all();
            $movedCount = 0;
            $failedCount = 0;

            foreach ($videos as $video) {
                $sourcePath = base_path($video->path);
                $targetPath = public_path($video->path);
                $targetDir = dirname($targetPath);

                $this->info("处理视频: {$video->title}");
                $this->info("源路径: {$sourcePath}");
                $this->info("目标路径: {$targetPath}");

                // 确保目标目录存在
                if (!File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }

                // 如果源文件存在，则移动它
                if (File::exists($sourcePath)) {
                    if (File::copy($sourcePath, $targetPath)) {
                        $this->info("成功移动文件");
                        $movedCount++;
                    } else {
                        $this->error("移动文件失败");
                        $failedCount++;
                    }
                } else {
                    $this->warn("源文件不存在");
                    $failedCount++;
                }

                $this->info("--------------------");
            }

            $this->info("移动完成！");
            $this->info("成功移动: {$movedCount} 个文件");
            $this->info("失败: {$failedCount} 个文件");

            Log::info('Video files moved', [
                'moved_count' => $movedCount,
                'failed_count' => $failedCount
            ]);

        } catch (\Exception $e) {
            $this->error('移动视频文件时发生错误：' . $e->getMessage());
            Log::error('Error moving video files:', [
                'error' => $e->getMessage()
            ]);
        }
    }
} 