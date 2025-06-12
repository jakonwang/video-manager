<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoDownload extends Model
{
    protected $fillable = [
        'video_id',
        'ip_address',
        'user_agent',
        'download_location',
        'country',
        'city',
        'is_success',
        'error_message',
    ];

    protected $casts = [
        'is_success' => 'boolean',
    ];

    /**
     * Get the video that was downloaded.
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Get the user who downloaded the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}