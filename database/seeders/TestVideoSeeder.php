<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\VideoCategory;

class TestVideoSeeder extends Seeder
{
    public function run()
    {
        // 确保分类存在
        $category = VideoCategory::firstOrCreate(
            ['id' => 1],
            [
                'name' => '测试分类',
                'description' => '这是一个测试分类'
            ]
        );

        // 创建测试视频
        Video::create([
            'category_id' => 1,
            'title' => '测试视频',
            'description' => '这是一个测试视频',
            'path' => 'test_video.mp4',
            'size' => 1024,
            'mime_type' => 'video/mp4',
            'processed' => true,
            'is_used' => false
        ]);
    }
} 