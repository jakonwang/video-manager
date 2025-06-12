@extends('admin.layouts.app')

@section('title', __('admin.edit_video'))

@section('content')
<div class="space-y-6">
    <!-- 页面头部 -->
    <div class="glass-effect rounded-xl p-4 shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                    <i class="fas fa-edit text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('admin.edit_video') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.edit_video_description') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.videos.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gradient-to-r from-gray-500 to-gray-600 text-white hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200 shadow-sm text-sm">
                <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
                {{ __('admin.back_to_list') }}
            </a>
        </div>
    </div>

    <!-- 编辑表单 -->
    <div class="glass-effect rounded-xl p-6 shadow-md hover-lift">
        <form action="{{ route('admin.videos.update', $video) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- 视频标题 -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-heading mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.video_title') }}
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}" required
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm"
                    placeholder="{{ __('admin.enter_video_title') }}">
                @error('title')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 视频描述 -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-align-left mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.video_description') }}
                </label>
                <textarea id="description" name="description" rows="3"
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 resize-none text-sm"
                    placeholder="{{ __('admin.enter_video_description') }}">{{ old('description', $video->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 视频分类 -->
            <div class="mb-6">
                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-folder mr-1.5 text-primary-500 text-xs"></i>{{ __('admin.video_category') }}
                </label>
                <select id="category_id" name="category_id" required
                    class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm">
                    <option value="">{{ __('admin.select_category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $video->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

                <!-- 标签 -->
                <div class="space-y-1">
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-tags mr-1.5 text-orange-500 text-xs"></i>
                        {{ __('admin.tags') }}
                    </label>
                    <input type="text" name="tags" id="tags" value="{{ old('tags', $video->tags) }}" 
                           class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-200 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm @error('tags') ring-red-500 @enderror" 
                           placeholder="{{ __('admin.enter_tags_separated_by_commas') }}">
                    @error('tags')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ __('admin.tags_help_text') }}
                    </p>
                </div>

            <!-- 提交按钮 -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.videos.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left mr-1.5 text-xs"></i>{{ __('admin.back') }}
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-primary-600 to-primary-700 border border-transparent rounded-lg text-sm font-medium text-white shadow-md hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                    <i class="fas fa-save mr-1.5 text-xs"></i>{{ __('admin.update_video') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection