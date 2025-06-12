<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckVideoStatus extends Command
{
    protected $signature = 'videos:check-status';
    protected $description = '检查视频和分类状态';

    public function handle()
    {
        $this->info('开始检查视频和分类状态...');

        try {
            // 检查所有分类
            $categories = VideoCategory::all();
            $this->info("\n分类列表：");
            foreach ($categories as $category) {
                $this->info("分类ID: {$category->id}, 名称: {$category->name}");
                
                // 检查该分类下的视频
                $videos = Video::where('category_id', $category->id)->get();
                $this->info("该分类下的视频数量: {$videos->count()}");
                
                foreach ($videos as $video) {
                    $this->info("  视频ID: {$video->id}");
                    $this->info("  标题: {$video->title}");
                    $this->info("  路径: {$video->path}");
                    $this->info("  处理状态: " . ($video->processed ? '已处理' : '未处理'));
                    $this->info("  使用状态: " . ($video->is_used ? '已使用' : '未使用'));
                    $this->info("  文件大小: {$video->size}");
                    $this->info("  MIME类型: {$video->mime_type}");
                    $this->info("  --------------------");
                }
            }

            // 检查未使用的视频
            $unusedVideos = Video::where('is_used', false)->where('processed', true)->get();
            $this->info("\n未使用的视频数量: {$unusedVideos->count()}");
            
            // 检查已处理的视频
            $processedVideos = Video::where('processed', true)->get();
            $this->info("已处理的视频数量: {$processedVideos->count()}");
            
            // 检查视频文件是否存在
            $this->info("\n检查视频文件：");
            foreach ($videos as $video) {
                $filePath = base_path($video->path);
                $exists = file_exists($filePath);
                $this->info("视频 {$video->title} 的文件路径: {$filePath}");
                $this->info("文件是否存在: " . ($exists ? '是' : '否'));
                if ($exists) {
                    $this->info("文件大小: " . filesize($filePath) . " 字节");
                }
                $this->info("--------------------");
            }

            Log::info('Video status check completed', [
                'categories_count' => $categories->count(),
                'videos_count' => $videos->count(),
                'unused_videos_count' => $unusedVideos->count(),
                'processed_videos_count' => $processedVideos->count()
            ]);

        } catch (\Exception $e) {
            $this->error('检查视频状态时发生错误：' . $e->getMessage());
            Log::error('Error checking video status:', [
                'error' => $e->getMessage()
            ]);
        }
    }
} 