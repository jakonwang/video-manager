<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 设置默认字符串长度以解决MySQL索引键长度限制
        Schema::defaultStringLength(191);
        
        // 修正会话路径分隔符
        \Illuminate\Support\Facades\Config::set(
            'session.files',
            str_replace('\\', '/', storage_path('framework/sessions'))
        );

        // 注册分页视图
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.tailwind');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}