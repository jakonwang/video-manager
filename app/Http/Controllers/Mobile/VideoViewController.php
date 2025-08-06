<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoIpDownload;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VideoViewController extends Controller
{
    /**
     * 显示分类的未使用视频
     *
     * @param Request $request
     * @param int $categoryId
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function showCategoryVideo(Request $request, int $categoryId)
    {
        $category = VideoCategory::findOrFail($categoryId);
        $ipAddress = $request->ip();
        
        // 启用数据库查询日志
        DB::enableQueryLog();
        
        // 记录分类信息
        Log::info('Category info:', [
            'category_id' => $categoryId,
            'category_exists' => $category ? true : false,
            'category_name' => $category ? $category->name : null
        ]);

        try {
            // 获取一个随机的未使用视频
            $video = Video::where('category_id', $categoryId)
                ->where('processed', true)
                ->where('is_used', false)
                ->inRandomOrder()
                ->first();

            // 记录查询日志
            Log::info('Random video query:', [
                'queries' => DB::getQueryLog(),
                'category_id' => $categoryId,
                'found_video' => $video ? $video->id : null,
                'video_details' => $video ? [
                    'id' => $video->id,
                    'title' => $video->title,
                    'processed' => $video->processed,
                    'is_used' => $video->is_used,
                    'path' => $video->path
                ] : null
            ]);
            
            if (!$video) {
                // 检查是否有任何视频
                $totalVideos = Video::where('category_id', $categoryId)->count();
                $processedVideos = Video::where('category_id', $categoryId)->where('processed', true)->count();
                $unusedVideos = Video::where('category_id', $categoryId)->where('is_used', false)->count();
                
                Log::info('No video found, checking counts:', [
                    'total_videos' => $totalVideos,
                    'processed_videos' => $processedVideos,
                    'unused_videos' => $unusedVideos,
                    'category_id' => $categoryId
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => __('mobile.video.no_video')
                    ], 404);
                }
                
                return view('mobile.no-video', ['category' => $category]);
            }

            // 检查视频文件是否存在（使用腾讯云 COS）
            $cosAdapter = app(\App\Services\CosAdapter::class);
            Log::info('Checking video file in COS:', [
                'video_id' => $video->id,
                'path' => $video->path,
                'exists' => $cosAdapter->exists($video->path),
                'size' => $cosAdapter->exists($video->path) ? $cosAdapter->size($video->path) : 0
            ]);

            if (!$cosAdapter->exists($video->path)) {
                Log::error('Video file not found in COS:', [
                    'video_id' => $video->id,
                    'path' => $video->path
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => __('mobile.video.file_not_found')
                    ], 404);
                }
                
                return view('mobile.no-video', ['category' => $category]);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'video' => [
                        'id' => $video->id,
                        'title' => $video->title,
                        'description' => $video->description,
                        'size' => $video->formatted_size,
                        'mime_type' => $video->mime_type,
                        'preview_url' => route('mobile.video.preview', $video->id),
                        'download_url' => route('mobile.video.download', $video->id)
                    ],
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name
                    ]
                ]);
            }
            
            return view('mobile.video-view', [
                'video' => $video,
                'category' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('Error in showCategoryVideo:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => __('mobile.common.error')
                ], 500);
            }

            return view('mobile.no-video', ['category' => $category]);
        }
    }
    
    /**
     * 预览视频
     *
     * @param Request $request
     * @param int $videoId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function preview(Request $request, int $videoId)
    {
        $video = Video::findOrFail($videoId);
        
        Log::info('Video preview request:', [
            'video_id' => $videoId,
            'video_details' => [
                'id' => $video->id,
                'title' => $video->title,
                'processed' => $video->processed,
                'path' => $video->path
            ]
        ]);
        
        // 检查视频是否已处理
        if (!$video->processed) {
            Log::error('Video not processed:', [
                'video_id' => $videoId
            ]);
            abort(404, '视频尚未处理完成');
        }
        
        // 使用腾讯云 COS 存储
        $cosAdapter = app(\App\Services\CosAdapter::class);
        
        if (!$cosAdapter->exists($video->path)) {
            Log::error('Video file not found in COS:', [
                'video_id' => $videoId,
                'path' => $video->path
            ]);
            abort(404, '视频文件不存在');
        }
        
        $fileSize = $cosAdapter->size($video->path);
        $mimeType = $video->mime_type ?: 'video/mp4';
        
        Log::info('Sending video file:', [
            'video_id' => $videoId,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType
        ]);
        
        // 支持范围请求以便视频可以快进
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $fileSize,
        ];
        
        $range = $request->header('Range');
        
        if ($range) {
            // 处理范围请求
            preg_match('/bytes=(\d+)-(\d*)/', $range, $matches);
            $start = intval($matches[1]);
            $end = $matches[2] ? intval($matches[2]) : $fileSize - 1;
            
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$fileSize}";
            $headers['Content-Length'] = $end - $start + 1;
            
            Log::info('Handling range request:', [
                'video_id' => $videoId,
                'range' => $range,
                'start' => $start,
                'end' => $end,
                'content_length' => $end - $start + 1
            ]);
            
            return response()->stream(function() use ($cosAdapter, $video, $start, $end) {
                // 从 COS 获取文件内容
                $fileContents = $cosAdapter->get($video->path);
                if ($fileContents === false) {
                    abort(500, '无法获取视频文件');
                }
                
                // 输出指定范围的内容
                echo substr($fileContents, $start, $end - $start + 1);
                flush();
            }, 206, $headers);
        }
        
        return response()->stream(function() use ($cosAdapter, $video) {
            // 从 COS 获取文件内容
            $fileContents = $cosAdapter->get($video->path);
            if ($fileContents === false) {
                abort(500, '无法获取视频文件');
            }
            
            echo $fileContents;
            flush();
        }, 200, $headers);
    }
    
    /**
     * 获取分类信息和可用视频数量
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryInfo(int $categoryId)
    {
        $category = VideoCategory::findOrFail($categoryId);
        $unusedCount = Video::getUnusedByCategory($categoryId)->count();
        
        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description
            ],
            'unused_videos_count' => $unusedCount,
            'has_available_videos' => $unusedCount > 0
        ]);
    }

    public function view($id)
    {
        $video = Video::findOrFail($id);
        
        // 检查视频是否已处理
        if (!$video->processed) {
            return response()->json([
                'success' => false,
                'message' => __('mobile.video.processing')
            ]);
        }

        // 检查视频文件是否存在（使用腾讯云 COS）
        $cosAdapter = app(\App\Services\CosAdapter::class);
        if (!$cosAdapter->exists($video->path)) {
            return response()->json([
                'success' => false,
                'message' => __('mobile.video.file_not_found')
            ]);
        }

        return response()->json([
            'success' => true,
            'video' => $video
        ]);
    }

    public function download($id)
    {
        $video = Video::findOrFail($id);
        $ipAddress = request()->ip();

        // 检查视频是否已处理
        if (!$video->processed) {
            return response()->json([
                'success' => false,
                'message' => __('mobile.video.processing')
            ]);
        }

        // 检查视频是否已被使用
        if ($video->is_used) {
            return response()->json([
                'success' => false,
                'message' => '该视频已被下载，不可重复下载'
            ]);
        }

        // 检查是否可以下载
        if (!VideoIpDownload::canDownload($ipAddress)) {
            $nextDownloadTime = VideoIpDownload::getNextDownloadTime($ipAddress);
            return response()->json([
                'success' => false,
                'message' => __('mobile.video.download_cooldown'),
                'next_download_time' => $nextDownloadTime
            ]);
        }

        // 检查视频文件是否存在（使用腾讯云 COS）
        $cosAdapter = app(\App\Services\CosAdapter::class);
        if (!$cosAdapter->exists($video->path)) {
            return response()->json([
                'success' => false,
                'message' => __('mobile.video.file_not_found')
            ]);
        }

        // 原子性标记为已使用
        $updated = Video::where('id', $video->id)->where('is_used', false)->update(['is_used' => true]);
        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => '该视频已被下载，不可重复下载'
            ]);
        }

        // 记录下载
        VideoIpDownload::create([
            'ip_address' => $ipAddress,
            'video_id' => $video->id,
            'downloaded_at' => now()
        ]);

        // 获取文件内容并返回下载
        $fileContents = $cosAdapter->get($video->path);
        if ($fileContents === false) {
            return response()->json([
                'success' => false,
                'message' => '无法获取视频文件'
            ], 500);
        }

        // 返回视频文件流
        $extension = pathinfo($video->path, PATHINFO_EXTENSION);
        $fileName = $video->title . '.' . $extension;
        return response($fileContents, 200, [
            'Content-Type' => $video->mime_type ?? 'video/mp4',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($fileContents)
        ]);
    }

    /**
     * 冷却倒计时页面
     */
    public function wait(Request $request, $categoryId)
    {
        $category = \App\Models\VideoCategory::findOrFail($categoryId);
        // 支持通过 query 参数传递 next_download_time
        $next = $request->query('next');
        $nextAvailableTime = $next ? \Carbon\Carbon::parse($next) : null;
        return view('mobile.video-wait', [
            'category' => $category,
            'nextAvailableTime' => $nextAvailableTime,
        ]);
    }
}