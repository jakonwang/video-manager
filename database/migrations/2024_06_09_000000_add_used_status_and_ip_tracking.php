<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 为videos表添加使用状态字段
        Schema::table('videos', function (Blueprint $table) {
            $table->boolean('is_used')->default(false)->after('processed');
        });

        // 创建IP下载记录表
        Schema::create('video_ip_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->string('ip_address');
            $table->timestamp('downloaded_at');
            $table->timestamps();
            
            // 添加索引以提高查询性能
            $table->index(['ip_address', 'downloaded_at']);
            $table->index(['video_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_ip_downloads');
        
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });
    }
};