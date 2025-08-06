<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoUpload;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function index(Request $request)
    {   
        $query = Video::with('category')->latest();

        // 分类筛选
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 状态筛选
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'processed':
                    $query->where('processed', true);
                    break;
                case 'processing':
                    $query->where('processed', false);
                    break;
            }
        }

        // 使用状态筛选
        if ($request->filled('usage_status')) {
            switch ($request->usage_status) {
                case 'used':
                    $query->where('is_used', true);
                    break;
                case 'unused':
                    $query->where('is_used', false);
                    break;
            }
        }

        // 搜索
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $videos = $query->paginate(10);
        $categories = VideoCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.videos.index', compact('videos', 'categories'));
    }

    public function create()
    {   
        $categories = VideoCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.videos.create', compact('categories'));
    }

    /**
     * 存储新创建的视频
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:video_categories,id',
            'videos' => 'required|array',
            'videos.*' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/x-flv,video/x-ms-wmv,video/webm|max:512000', // 最大500MB
        ]);

        try {
            $videos = $request->file('videos');
            $successResults = [];
            $failResults = [];
            $successCount = 0;
            $failCount = 0;

            // 确保临时目录存在
            $tempDir = storage_path('app/temp/videos');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            foreach ($videos as $video) {
                try {
                    // 获取文件信息
                    $originalFilename = $video->getClientOriginalName();
                    $fileSize = $video->getSize();
                    $mimeType = $video->getMimeType();
                    $fileType = $video->getClientOriginalExtension();
                    
                    // 生成唯一文件名
                    $fileName = uniqid() . '_' . time() . '.' . $fileType;
                    $tempFilePath = $tempDir . '/' . $fileName;
                    
                    // 保存到临时目录
                    if ($video->move($tempDir, $fileName)) {
                        // 记录日志
                        Log::info('视频文件已保存到临时目录', [
                            'file' => $fileName,
                            'size' => $fileSize,
                            'type' => $mimeType
                        ]);
                        
                        // 创建视频记录
                        $videoRecord = new Video();
                        $videoRecord->title = $request->title;
                        $videoRecord->description = $request->description;
                        $videoRecord->category_id = $request->category_id;
                        $videoRecord->processed = false; // 标记为未处理
                        $videoRecord->path = $tempFilePath; // 设置临时文件路径
                        $videoRecord->size = $fileSize; // 设置文件大小
                        $videoRecord->mime_type = $mimeType; // 设置MIME类型
                        $videoRecord->save();
                        
                        // 准备队列任务数据
                        $videoData = [
                            'video_id' => $videoRecord->id,
                            'title' => $request->title,
                            'description' => $request->description,
                            'category_id' => $request->category_id
                        ];
                        
                        // 分发队列任务
                        ProcessVideoUpload::dispatch($videoData, $tempFilePath, $originalFilename, $fileSize, $mimeType);
                        
                        $successResults[] = [
                            'id' => $videoRecord->id,
                            'title' => $videoRecord->title,
                            'original_filename' => $originalFilename
                        ];
                        
                        $successCount++;
                    } else {
                        throw new \Exception('无法保存视频文件到临时目录');
                    }
                } catch (\Exception $e) {
                    Log::error('单个视频处理失败', [
                        'file' => $video->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                    
                    $failResults[] = [
                        'filename' => $video->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ];
                    
                    $failCount++;
                }
            }
            
            // 根据上传结果返回不同的消息
            $message = '';
            if ($successCount > 0 && $failCount > 0) {
                $message = $successCount . '个视频已加入处理队列，' . $failCount . '个视频上传失败';
            } elseif ($successCount > 0) {
                $message = $successCount . '个视频已成功加入处理队列';
            } else {
                $message = '所有视频上传失败';
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => [
                        'fail_results' => $failResults
                    ]
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'success_count' => $successCount,
                    'fail_count' => $failCount,
                    'success_results' => $successResults,
                    'fail_results' => $failResults
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('视频批量上传失败', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '视频上传失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Video $video)
    {
        $categories = VideoCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.videos.edit', compact('video', 'categories'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:video_categories,id'
        ]);

        $video->update($validated);

        return redirect()->route('admin.videos.index')
            ->with('success', '视频信息更新成功');
    }

    public function preview(Video $video)
    {
        // 检查视频是否已处理完成
        if (!$video->processed) {
            return response()->json([
                'success' => false,
                'message' => '视频还在处理中，无法预览'
            ], 400);
        }

        // 检查文件是否存在
        // 视频文件存储在 public/videos 目录下
        $videoPath = $video->path;
        $fullPath = public_path($videoPath);
        
        if (!file_exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => '视频文件不存在'
            ], 404);
        }

        // 返回视频预览信息
        // 构建正确的URL，直接使用 /videos/ 路径
        $baseUrl = request()->getSchemeAndHttpHost();
        $videoUrl = $baseUrl . '/' . $videoPath;
        
        // 调试信息
        \Illuminate\Support\Facades\Log::info('视频预览请求', [
            'video_id' => $video->id,
            'path' => $videoPath,
            'full_path' => $videoUrl,
            'exists' => file_exists($fullPath),
            'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0
        ]);
        
        return response()->json([
            'success' => true,
            'title' => $video->title,
            'url' => $videoUrl,  // 确保字段名为'url'，与前端期望的一致
            'mime_type' => $video->mime_type ?? 'video/mp4',
            'description' => $video->description,
            'size' => $video->formatted_size,
            'category' => $video->category->name ?? '未分类'
        ]);
    }

    public function download(Video $video)
    {
        // 检查视频是否已处理完成
        if (!$video->processed) {
            return response()->json([
                'success' => false,
                'message' => '视频还在处理中，无法下载'
            ], 400);
        }

        // 检查是否启用 COS
        $useCos = \App\Models\Setting::get('use_cos', false);
        
        if ($useCos) {
            // 使用腾讯云 COS 下载文件
            $cosAdapter = app('cos.adapter');
            
            if (!$cosAdapter) {
                return response()->json([
                    'success' => false,
                    'message' => '腾讯云 COS 配置不完整，无法下载文件'
                ], 500);
            }
            
            // 检查文件是否存在
            if (!$cosAdapter->exists($video->path)) {
                // 记录错误日志
                \Illuminate\Support\Facades\Log::error('视频下载失败：文件不存在', [
                    'video_id' => $video->id,
                    'path' => $video->path
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => '视频文件不存在'
                ], 404);
            }

            // 获取文件内容
            $fileContents = $cosAdapter->get($video->path);
            if ($fileContents === false) {
                return response()->json([
                    'success' => false,
                    'message' => '无法获取视频文件'
                ], 500);
            }
        } else {
            // 使用本地存储下载文件
            $localPath = storage_path('app/public/' . $video->path);
            
            if (!file_exists($localPath)) {
                // 记录错误日志
                \Illuminate\Support\Facades\Log::error('视频下载失败：本地文件不存在', [
                    'video_id' => $video->id,
                    'path' => $localPath
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => '视频文件不存在'
                ], 404);
            }

            // 获取文件内容
            $fileContents = file_get_contents($localPath);
            if ($fileContents === false) {
                return response()->json([
                    'success' => false,
                    'message' => '无法读取视频文件'
                ], 500);
            }
        }

        // 获取文件扩展名
        $extension = pathinfo($video->path, PATHINFO_EXTENSION);
        $filename = $video->title . '.' . $extension;
        
        // 记录下载日志
        \Illuminate\Support\Facades\Log::info('视频下载请求', [
            'video_id' => $video->id,
            'path' => $video->path,
            'file_size' => strlen($fileContents),
            'storage' => $useCos ? 'COS' : 'Local'
        ]);
        
        // 返回文件下载响应
        return response($fileContents, 200, [
            'Content-Type' => $video->mime_type ?? 'video/mp4',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($fileContents)
        ]);
    }

    public function destroy(Video $video)
    {
        // 检查是否启用 COS
        $useCos = \App\Models\Setting::get('use_cos', false);
        
        if ($useCos) {
            // 使用腾讯云 COS 删除文件
            $cosAdapter = app('cos.adapter');
            if ($cosAdapter && $cosAdapter->exists($video->path)) {
                $cosAdapter->delete($video->path);
            }
        } else {
            // 使用本地存储删除文件
            $localPath = storage_path('app/public/' . $video->path);
            if (file_exists($localPath)) {
                unlink($localPath);
            }
        }
        
        $video->delete();

        return redirect()->route('admin.videos.index')
            ->with('success', '视频删除成功');
    }

    /**
     * 批量更新视频使用状态
     */
    public function batchUpdateUsage(Request $request)
    {
        $ids = $request->input('ids');
        $isUsed = $request->input('is_used');

        Video::whereIn('id', $ids)->update(['is_used' => $isUsed]);

        return response()->json(['success' => true]);
    }

    /**
     * 批量删除视频
     */
    public function batchDelete(Request $request)
    {
        $ids = $request->input('ids');

        Video::whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }
}