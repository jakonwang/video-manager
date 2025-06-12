@extends('admin.layouts.app')

@section('title', __('admin.create_category'))

@section('content')
<div class="space-y-6">
    <div class="glass-effect rounded-xl p-4 shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-md">
                    <i class="fas fa-folder-plus text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('admin.create_category') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">创建新的视频分类</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 创建表单 -->
    <div class="glass-effect rounded-xl p-6 shadow-md hover-lift">
        <form action="{{ route('admin.video-categories.store') }}" method="POST">
            @csrf
            
            <!-- 分类名称 -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-tag mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.category_name') }}
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm"
                    placeholder="{{ __('admin.enter_category_name') }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 分类描述 -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-align-left mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.description') }}
                </label>
                <textarea id="description" name="description" rows="3"
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 resize-none text-sm"
                    placeholder="{{ __('admin.enter_description') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 图标 -->
            <div class="mb-6">
                <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-icons mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.icon_class') }}
                </label>
                <input type="text" id="icon" name="icon" value="{{ old('icon', 'fas fa-folder') }}" required
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm"
                    placeholder="{{ __('admin.enter_icon_class') }}">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('admin.icon_class_help') }}</p>
                @error('icon')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 排序 -->
            <div class="mb-6">
                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-sort mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.sort_order') }}
                </label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm"
                    placeholder="{{ __('admin.enter_sort_order') }}">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('admin.sort_order_help') }}</p>
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 状态 -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-toggle-on mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.status') }}
                </label>
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">
                        {{ __('admin.active') }}
                    </label>
                </div>
            </div>

            <!-- 提交按钮 -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.video-categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left mr-1.5 text-xs"></i>{{ __('admin.back') }}
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-primary-600 to-primary-700 border border-transparent rounded-lg text-sm font-medium text-white shadow-md hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                    <i class="fas fa-save mr-1.5 text-xs"></i>{{ __('admin.create_category') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection