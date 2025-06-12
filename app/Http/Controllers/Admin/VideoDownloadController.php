<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoDownload;
use Illuminate\Http\Request;

class VideoDownloadController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoDownload::with('video')->latest();

        // 搜索功能
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('video', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })->orWhere('ip_address', 'like', "%{$search}%")
              ->orWhere('country', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        }

        // 状态筛选
        if ($request->filled('is_success')) {
            $query->where('is_success', $request->is_success === '1');
        }

        // 日期范围筛选
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $downloads = $query->paginate(15)->withQueryString();

        // 统计数据
        $stats = [
            'total' => VideoDownload::count(),
            'success' => VideoDownload::where('is_success', true)->count(),
            'failed' => VideoDownload::where('is_success', false)->count(),
            'today' => VideoDownload::whereDate('created_at', today())->count(),
        ];

        return view('admin.downloads.index', compact('downloads', 'stats'));
    }

    public function show(VideoDownload $download)
    {
        $download->load('video');
        return view('admin.downloads.show', compact('download'));
    }

    public function destroy(VideoDownload $download)
    {
        $download->delete();
        return redirect()
            ->route('admin.downloads.index')
            ->with('success', '下载记录删除成功');
    }

    public function clear()
    {
        // 只保留最近30天的记录
        VideoDownload::where('created_at', '<', now()->subDays(30))->delete();
        
        return redirect()
            ->route('admin.downloads.index')
            ->with('success', '已清理30天前的下载记录');
    }
} 