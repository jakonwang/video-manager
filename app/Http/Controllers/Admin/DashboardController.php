<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Category;
use App\Models\VideoIpDownload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 总视频数
        $totalVideos = Video::count();
        // 已处理视频数
        $processedVideos = Video::where('processed', true)->count();
        // 分类总数
        $totalCategories = Category::count();
        // 已使用视频数
        $usedVideos = Video::where('is_used', true)->count();

        // 最近上传视频
        $recentVideos = Video::with('category')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // 分类统计（每个分类下视频数量，取前5）
        $categoryStats = Category::withCount('videos')
            ->orderByDesc('videos_count')
            ->take(5)
            ->get();

        // 近30天每天上传视频数量
        $dailyUploads = Video::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // 近30天每天下载视频数量
        $dailyDownloads = VideoIpDownload::select(
                DB::raw('DATE(downloaded_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('downloaded_at', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        return view('admin.dashboard', [
            'stats' => [
                'total_videos' => $totalVideos,
                'processed_videos' => $processedVideos,
                'total_categories' => $totalCategories,
                'used_videos' => $usedVideos,
            ],
            'recentVideos' => $recentVideos,
            'categoryStats' => $categoryStats,
            'dailyUploads' => $dailyUploads,
            'dailyDownloads' => $dailyDownloads,
        ]);
    }
} 