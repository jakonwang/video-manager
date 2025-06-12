@extends('admin.layouts.app')
@section('title', __('admin.system_settings'))

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- 页面头部 -->
    <div class="flex items-center space-x-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-md">
            <i class="fas fa-cog text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                {{ __('admin.system_settings') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.manage_system_configuration') }}</p>
        </div>
    </div>

    <!-- 设置表单 -->
    <div class="glass-effect rounded-xl shadow-md p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 基本设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-globe mr-2 text-primary-600 text-sm"></i>
                    {{ __('admin.basic_settings') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 网站名称 -->
                    <div class="space-y-1.5">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-tag mr-1.5 text-primary-500 text-xs"></i>
                            {{ __('admin.site_name') }}
                        </label>
                        <input type="text" name="site_name" id="site_name" 
                            value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- 管理员邮箱 -->
                    <div class="space-y-1.5">
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-envelope mr-1.5 text-primary-500 text-xs"></i>
                            {{ __('admin.admin_email') }}
                        </label>
                        <input type="email" name="admin_email" id="admin_email" 
                            value="{{ old('admin_email', $settings['admin_email'] ?? '') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>
            </div>

            <!-- 文件设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-file-upload mr-2 text-accent-600 text-sm"></i>
                    {{ __('admin.file_settings') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 最大文件大小 -->
                    <div class="space-y-1.5">
                        <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-weight-hanging mr-1.5 text-accent-500 text-xs"></i>
                            {{ __('admin.max_file_size') }} (MB)
                        </label>
                        <input type="number" name="max_file_size" id="max_file_size" 
                            value="{{ old('max_file_size', $settings['max_file_size'] ?? 100) }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- 允许的文件类型 -->
                    <div class="space-y-1.5">
                        <label for="allowed_file_types" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-file-video mr-1.5 text-accent-500 text-xs"></i>
                            {{ __('admin.allowed_file_types') }}
                        </label>
                        <input type="text" name="allowed_file_types" id="allowed_file_types" 
                            value="{{ old('allowed_file_types', $settings['allowed_file_types'] ?? 'mp4,mov,avi') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all duration-200">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="fas fa-info-circle mr-1.5 text-xs"></i>
                            {{ __('admin.file_types_help') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 系统设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cogs mr-2 text-indigo-600 text-sm"></i>
                    {{ __('admin.system_configuration') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 系统语言 -->
                    <div class="space-y-1.5">
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-language mr-1.5 text-indigo-500 text-xs"></i>
                            {{ __('admin.system_language') }}
                        </label>
                        <select name="language" id="language"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <option value="zh" {{ (old('language', $settings['language'] ?? 'zh') == 'zh') ? 'selected' : '' }}>{{ __('admin.chinese') }}</option>
                            <option value="en" {{ (old('language', $settings['language'] ?? 'zh') == 'en') ? 'selected' : '' }}>{{ __('admin.english') }}</option>
                            <option value="vi" {{ (old('language', $settings['language'] ?? 'zh') == 'vi') ? 'selected' : '' }}>{{ __('admin.vietnamese') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 操作按钮 -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="reset" class="inline-flex items-center space-x-2 rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-undo text-sm"></i>
                    <span>{{ __('admin.reset') }}</span>
                </button>
                <button type="submit" class="inline-flex items-center space-x-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-save text-sm"></i>
                    <span>{{ __('admin.save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection