<?php

namespace App\Http\Controllers;

use App\Models\VideoCategory;
use Illuminate\Http\Request;

class VideoCategoryController extends Controller
{
    /**
     * 显示视频分类列表
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = VideoCategory::all();
        return view('video-categories.index', compact('categories'));
    }

    /**
     * 显示特定分类下的视频
     *
     * @param VideoCategory $videoCategory
     * @return \Illuminate\View\View
     */
    public function videos(VideoCategory $videoCategory)
    {
        $videos = $videoCategory->videos()->paginate(12);
        return view('video-categories.videos', compact('videoCategory', 'videos'));
    }
} 