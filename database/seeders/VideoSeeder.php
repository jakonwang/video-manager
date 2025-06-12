<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\VideoCategory;

class VideoSeeder extends Seeder
{
    public function run()
    {
        // 创建一个测试分类
        $category = VideoCategory::firstOrCreate(
            ['name' => '测试分类'],
            [
                'description' => '用于测试的视频分类',
                'icon_class' => 'fas fa-video',
                'sort_order' => 1,
                'is_active' => true
            ]
        );

        // 创建多个测试视频
        $videos = [
            [
                'title' => '测试视频1',
                'description' => '这是第一个测试视频',
                'path' => 'videos/test1.mp4',
                'size' => 1024,
                'mime_type' => 'video/mp4',
                'processed' => true,
                'is_used' => false,
                'category_id' => $category->id
            ],
            [
                'title' => '测试视频2',
                'description' => '这是第二个测试视频',
                'path' => 'videos/test2.mp4',
                'size' => 1024,
                'mime_type' => 'video/mp4',
                'processed' => true,
                'is_used' => false,
                'category_id' => $category->id
            ],
            [
                'title' => '测试视频3',
                'description' => '这是第三个测试视频',
                'path' => 'videos/test3.mp4',
                'size' => 1024,
                'mime_type' => 'video/mp4',
                'processed' => true,
                'is_used' => false,
                'category_id' => $category->id
            ]
        ];

        foreach ($videos as $videoData) {
            Video::firstOrCreate(
                ['title' => $videoData['title']],
                $videoData
            );
        }
    }
} 