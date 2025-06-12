<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VideoIpDownload extends Model
{
    protected $fillable = [
        'video_id',
        'ip_address',
        'downloaded_at'
    ];

    protected $casts = [
        'downloaded_at' => 'datetime'
    ];

    /**
     * 获取下载记录所属的视频
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * 检查IP是否可以下载新视频（距离上次下载超过10分钟）
     *
     * @param string $ipAddress
     * @return bool
     */
    public static function canDownload(string $ipAddress): bool
    {
        $lastDownload = self::where('ip_address', $ipAddress)
            ->orderBy('downloaded_at', 'desc')
            ->first();

        Log::info('Checking IP download status:', [
            'ip_address' => $ipAddress,
            'last_download' => $lastDownload ? [
                'video_id' => $lastDownload->video_id,
                'downloaded_at' => $lastDownload->downloaded_at,
                'minutes_ago' => $lastDownload->downloaded_at->diffInMinutes(now())
            ] : null
        ]);

        if (!$lastDownload) {
            return true;
        }

        $canDownload = $lastDownload->downloaded_at->diffInMinutes(now()) >= 10;
        
        Log::info('IP download check result:', [
            'ip_address' => $ipAddress,
            'can_download' => $canDownload,
            'minutes_since_last_download' => $lastDownload->downloaded_at->diffInMinutes(now())
        ]);

        return $canDownload;
    }

    /**
     * 获取IP下次可以下载的时间
     *
     * @param string $ipAddress
     * @return Carbon|null
     */
    public static function getNextAvailableTime(string $ipAddress): ?Carbon
    {
        $lastDownload = self::where('ip_address', $ipAddress)
            ->orderBy('downloaded_at', 'desc')
            ->first();

        if (!$lastDownload) {
            return null;
        }

        $nextTime = $lastDownload->downloaded_at->addMinutes(10);
        
        Log::info('Calculating next available time:', [
            'ip_address' => $ipAddress,
            'last_download' => $lastDownload->downloaded_at,
            'next_available_time' => $nextTime,
            'is_future' => $nextTime->isFuture()
        ]);
        
        return $nextTime->isFuture() ? $nextTime : null;
    }

    /**
     * 记录下载
     *
     * @param int $videoId
     * @param string $ipAddress
     * @return self
     */
    public static function recordDownload(int $videoId, string $ipAddress): self
    {
        Log::info('Recording IP download:', [
            'video_id' => $videoId,
            'ip_address' => $ipAddress
        ]);

        $record = self::create([
            'video_id' => $videoId,
            'ip_address' => $ipAddress,
            'downloaded_at' => now()
        ]);

        Log::info('IP download recorded:', [
            'record_id' => $record->id,
            'video_id' => $videoId,
            'ip_address' => $ipAddress,
            'downloaded_at' => $record->downloaded_at
        ]);

        return $record;
    }

    public static function getNextDownloadTime($ipAddress)
    {
        $lastDownload = self::where('ip_address', $ipAddress)
            ->orderByDesc('downloaded_at')
            ->first();

        if (!$lastDownload) {
            return now();
        }

        // 10分钟冷却
        $nextTime = $lastDownload->downloaded_at->addMinutes(10);
        return $nextTime;
    }
}