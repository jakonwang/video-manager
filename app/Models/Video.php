<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\VideoIpDownload;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'path',
        'size',
        'mime_type',
        'processed',
        'is_used',
        'last_downloaded_at',
        'category_id'
    ];

    protected $casts = [
        'last_downloaded_at' => 'datetime',
        'processed' => 'boolean',
        'is_used' => 'boolean'
    ];

    /**
     * 获取视频所属的分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(VideoCategory::class);
    }

    /**
     * Get the downloads for the video.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(VideoDownload::class);
    }

    /**
     * Check if the video can be downloaded by the user.
     *
     * @param string $userId
     * @return bool
     */
    public function canBeDownloadedBy($userId)
    {
        // 检查用户是否在10分钟内已经下载过视频
        $key = "video-download:{$userId}";
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return false;
        }

        // 检查IP是否在10分钟内已经下载过视频
        $ipAddress = request()->ip();
        if (!$this->canBeDownloadedByIp($ipAddress)) {
            return false;
        }

        return true;
    }

    /**
     * Record a download for this video.
     *
     * @param string $userId
     * @return void
     */
    public function recordDownload($userId)
    {
        // 记录下载时间
        $this->update([
            'last_downloaded_at' => now()
        ]);

        // 创建下载记录
        $this->downloads()->create([
            'user_id' => $userId,
            'downloaded_at' => now()
        ]);

        // 添加到限流器
        $key = "video-download:{$userId}";
        RateLimiter::hit($key, 600); // 10分钟限制

        // 记录IP下载
        $ipAddress = request()->ip();
        $this->recordIpDownload($ipAddress);
    }

    /**
     * Get the video file URL.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        try {
            // 检查是否启用 COS
            $useCos = Setting::get('use_cos', false);
            
            if ($useCos) {
                try {
                    // 尝试使用 COS 适配器
                    $cosAdapter = app('cos.adapter');
                    if ($cosAdapter) {
                        return $cosAdapter->url($this->path);
                    }
                } catch (\Exception $e) {
                    // 如果 COS 不可用，回退到本地存储
                    Log::warning('COS 存储不可用，回退到本地存储', [
                        'video_id' => $this->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // 使用本地存储
            return Storage::disk('public')->url($this->path);
            
        } catch (\Exception $e) {
            // 如果所有存储都失败，返回一个默认的 URL
            Log::error('视频 URL 获取失败', [
                'video_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return asset('videos/default.mp4');
        }
    }

    /**
     * Get the video thumbnail URL.
     *
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        // 返回一个默认的视频缩略图占位符
        return 'https://via.placeholder.com/300x200/4F46E5/FFFFFF?text=Video';
    }

    /**
     * Get the formatted file size.
     *
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        
        if ($bytes == 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor(log($bytes, 1024));
        $factor = min($factor, count($units) - 1);
        
        return sprintf('%.2f', $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }

    /**
     * 获取IP下载记录关联
     */
    public function ipDownloads()
    {
        return $this->hasMany(VideoIpDownload::class);
    }

    /**
     * 检查IP是否可以下载此视频（基于10分钟限制）
     *
     * @param string $ipAddress
     * @return bool
     */
    public function canBeDownloadedByIp(string $ipAddress): bool
    {
        return VideoIpDownload::canDownload($ipAddress);
    }

    /**
     * 记录IP下载并标记视频为已使用
     *
     * @param string $ipAddress
     * @return void
     */
    public function recordIpDownload(string $ipAddress): void
    {
        // 记录IP下载
        VideoIpDownload::recordDownload($this->id, $ipAddress);
        
        // 标记视频为已使用
        $this->update([
            'is_used' => true,
            'last_downloaded_at' => now()
        ]);
    }

    /**
     * 获取分类中未使用的视频
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getUnusedByCategory(int $categoryId)
    {
        return self::where('category_id', $categoryId)
            ->where('is_used', false)
            ->where('processed', true)
            ->orderBy('created_at', 'asc');
    }

    /**
     * 获取分类中第一个未使用的视频
     *
     * @param int $categoryId
     * @return Video|null
     */
    public static function getFirstUnusedByCategory(int $categoryId): ?Video
    {
        return self::getUnusedByCategory($categoryId)->first();
    }

    /**
     * 检查分类是否有未使用的视频
     *
     * @param int $categoryId
     * @return bool
     */
    public static function hasUnusedInCategory(int $categoryId): bool
    {
        return self::getUnusedByCategory($categoryId)->exists();
    }

    /**
     * 标记视频为已使用
     *
     * @return bool
     */
    public function markAsUsed(): bool
    {
        Log::info('Marking video as used:', [
            'video_id' => $this->id,
            'current_is_used' => $this->is_used,
            'current_last_downloaded_at' => $this->last_downloaded_at
        ]);

        try {
            $result = $this->update([
                'is_used' => true,
                'last_downloaded_at' => now()
            ]);

            // 重新加载模型以获取最新状态
            $this->refresh();

            Log::info('Video marked as used result:', [
                'video_id' => $this->id,
                'result' => $result,
                'new_is_used' => $this->is_used,
                'new_last_downloaded_at' => $this->last_downloaded_at
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error marking video as used:', [
                'video_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}