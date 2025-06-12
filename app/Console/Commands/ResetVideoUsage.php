<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class ResetVideoUsage extends Command
{
    protected $signature = 'videos:reset-usage';
    protected $description = '重置所有视频的使用状态';

    public function handle()
    {
        $this->info('开始重置视频使用状态...');

        try {
            $count = Video::where('is_used', true)->update([
                'is_used' => false,
                'last_downloaded_at' => null
            ]);

            $this->info("成功重置 {$count} 个视频的使用状态");

            Log::info('Video usage reset completed', [
                'reset_count' => $count
            ]);
        } catch (\Exception $e) {
            $this->error('重置视频使用状态时发生错误：' . $e->getMessage());
            Log::error('Error resetting video usage:', [
                'error' => $e->getMessage()
            ]);
        }
    }
} 