<?php

namespace App\Jobs;

use App\Models\Video;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务尝试次数
     */
    public $tries = 3;

    /**
     * 任务可以运行的最大秒数
     */
    public $timeout = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private array $videoData,
        private string $tempFilePath,
        private string $originalFilename,
        private int $fileSize,
        private string $mimeType
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('开始处理视频上传任务', [
                'file' => $this->originalFilename,
                'title' => $this->videoData['title']
            ]);

            // 检查临时文件是否存在
            if (!file_exists($this->tempFilePath)) {
                throw new Exception("临时文件不存在: {$this->tempFilePath}");
            }

            // 查找视频记录
            $video = null;
            if (isset($this->videoData['video_id'])) {
                $video = Video::find($this->videoData['video_id']);
            }

            if (!$video) {
                // 如果没有找到视频记录，创建一个新的
                $video = new Video();
                $video->title = $this->videoData['title'];
                $video->description = $this->videoData['description'] ?? null;
                $video->category_id = $this->videoData['category_id'];
                $video->processed = false;
                $video->save();
                
                Log::info('创建新的视频记录', ['video_id' => $video->id]);
            }

            // 确保目标目录存在
            $targetDir = 'videos/' . date('Y/m/d');
            
            // 生成唯一文件名
            $fileName = pathinfo($this->tempFilePath, PATHINFO_BASENAME);
            $targetPath = $targetDir . '/' . $fileName;

            // 检查是否启用 COS
            $useCos = \App\Models\Setting::get('use_cos', false);
            
            if ($useCos) {
                // 使用腾讯云 COS 上传文件
                $cosAdapter = app('cos.adapter');
                
                if (!$cosAdapter) {
                    throw new Exception('腾讯云 COS 配置不完整，无法上传文件');
                }
                
                // 对于大文件，使用分片上传
                if ($this->fileSize > 100 * 1024 * 1024) { // 大于100MB的文件
                    $uploadSuccess = $cosAdapter->putLargeFile($targetPath, $this->tempFilePath);
                } else {
                    $uploadSuccess = $cosAdapter->put($targetPath, file_get_contents($this->tempFilePath));
                }

                if (!$uploadSuccess) {
                    throw new Exception('无法上传视频文件到腾讯云COS');
                }
            } else {
                // 使用本地存储
                $localPath = 'public/' . $targetPath;
                $uploadSuccess = Storage::disk('local')->put($localPath, file_get_contents($this->tempFilePath));
                
                if (!$uploadSuccess) {
                    throw new Exception('无法保存视频文件到本地存储');
                }
                
                $targetPath = $localPath;
            }

            // 删除临时文件
            @unlink($this->tempFilePath);

            // 更新视频记录 - 保存相对路径，便于访问
            $video->path = $targetPath;
            $video->size = $this->fileSize;
            $video->mime_type = $this->mimeType;
            
            // 在这里可以添加视频处理的逻辑，比如转码、压缩、生成缩略图等
            // ...

            // 更新处理状态为已完成
            $video->processed = true;
            $video->save();

            Log::info('视频处理完成', [
                'video_id' => $video->id,
                'title' => $video->title,
                'path' => $video->path,
                'storage' => $useCos ? 'COS' : 'Local'
            ]);

        } catch (Exception $e) {
            Log::error('视频处理失败', [
                'error' => $e->getMessage(),
                'title' => $this->videoData['title'] ?? 'unknown',
                'file' => $this->originalFilename
            ]);

            // 如果已创建视频记录，则更新状态为失败
            if (isset($video) && $video->id) {
                $video->processed = false;
                $video->save();
            }

            throw $e;
        }
    }

    /**
     * 处理失败的任务
     */
    public function failed(Exception $exception): void
    {
        Log::error('视频上传任务失败', [
            'error' => $exception->getMessage(),
            'title' => $this->videoData['title'] ?? 'unknown',
            'file' => $this->originalFilename
        ]);
        
        // 尝试清理临时文件
        if (file_exists($this->tempFilePath)) {
            @unlink($this->tempFilePath);
        }
    }
}