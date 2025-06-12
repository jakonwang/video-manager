<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoCategory;
use Illuminate\Http\Request;

class VideoCategoryController extends Controller
{
    /**
     * 显示视频分类列表
     */
    public function index()
    {
        $categories = VideoCategory::withCount('videos')->orderBy('sort_order')->paginate(10);
        return view('admin.video-categories.index', compact('categories'));
    }

    /**
     * 显示创建视频分类表单
     */
    public function create()
    {
        return view('admin.video-categories.create');
    }

    /**
     * 保存新的视频分类
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:video_categories',
            'description' => 'nullable|string',
            'icon_class' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        VideoCategory::create($validated);

        return redirect()
            ->route('admin.video-categories.index')
            ->with('success', '分类创建成功');
    }

    /**
     * 显示编辑视频分类表单
     */
    public function edit(VideoCategory $videoCategory)
    {
        return view('admin.video-categories.edit', compact('videoCategory'));
    }

    /**
     * 更新视频分类
     */
    public function update(Request $request, VideoCategory $videoCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:video_categories,name,' . $videoCategory->id,
            'description' => 'nullable|string',
            'icon_class' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $videoCategory->update($validated);

        return redirect()
            ->route('admin.video-categories.index')
            ->with('success', '分类更新成功');
    }

    /**
     * 删除视频分类
     */
    public function destroy(VideoCategory $videoCategory)
    {
        // 检查分类下是否有视频
        if ($videoCategory->videos()->exists()) {
            return back()->with('error', '无法删除该分类，因为还有视频使用此分类');
        }

        $videoCategory->delete();

        return redirect()
            ->route('admin.video-categories.index')
            ->with('success', '分类删除成功');
    }

    /**
     * 显示分类下的视频列表
     */
    public function videos(VideoCategory $videoCategory, Request $request)
    {
        $query = $videoCategory->videos()->with('category')->latest();

        // 处理状态筛选
        if ($request->filled('processed')) {
            $query->where('processed', $request->processed === '1');
        }

        // 使用状态筛选
        if ($request->filled('is_used')) {
            $query->where('is_used', $request->is_used === '1');
        }

        // 搜索功能
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $videos = $query->paginate(12)->withQueryString();
        $categories = VideoCategory::orderBy('name')->get();

        return view('admin.video-categories.videos', compact('videoCategory', 'videos', 'categories'));
    }
}