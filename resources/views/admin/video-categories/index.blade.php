@extends('admin.layouts.app')

@section('title', __('admin.category_management'))

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- 页面头部 -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-accent-500 to-accent-600 shadow-md">
                <i class="fas fa-folder text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    {{ __('admin.category_management') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">管理视频分类和组织结构</p>
            </div>
        </div>
        <a href="{{ route('admin.video-categories.create') }}" class="inline-flex items-center space-x-2 rounded-lg bg-gradient-to-r from-accent-600 to-accent-700 px-4 py-2 text-sm font-semibold text-white shadow-md hover:from-accent-700 hover:to-accent-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-600 transition-all duration-200 hover:shadow-lg">
            <i class="fas fa-plus text-sm"></i>
            <span>{{ __('admin.create_category') }}</span>
        </a>
    </div>

    <!-- 分类列表卡片 -->
    <div class="glass-effect rounded-xl shadow-md overflow-hidden">
        @if($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-tag mr-1.5 text-xs"></i>{{ __('admin.category_info') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-video mr-1.5 text-xs"></i>{{ __('admin.video_count') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-align-left mr-1.5 text-xs"></i>{{ __('admin.description') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-sort mr-1.5 text-xs"></i>{{ __('admin.sort_order') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-1.5 text-xs"></i>{{ __('admin.status') }}
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-tools mr-1.5 text-xs"></i>{{ __('admin.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            
                        @foreach($categories as $index => $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <!-- 分类信息 -->
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                                            <i class="{{ $category->icon ?? 'fas fa-tag' }} text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $category->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.created_at') }}: {{ $category->created_at->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 视频数量 -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    <i class="fas fa-video mr-1.5 text-xs"></i>
                                    {{ $category->videos_count }} {{ __('admin.videos') }}
                                </span>
                            </td>
                            
                            <!-- 描述 -->
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-900 dark:text-white max-w-xs truncate">{{ $category->description ?: __('admin.no_description') }}</p>
                            </td>
                            
                            <!-- 排序 -->
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->sort_order }}</span>
                            </td>
                            
                            <!-- 状态 -->
                            <td class="px-4 py-3">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                        {{ __('admin.active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fas fa-times-circle mr-1.5 text-xs"></i>
                                        {{ __('admin.inactive') }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- 操作按钮 -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('mobile.category.video', ['categoryId' => $category->id]) }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-purple-100 text-purple-600 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50 transition-colors duration-200 group" title="{{ __('admin.view_videos') }}">
                                        <i class="fas fa-eye text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                    </a>
                                    <a href="{{ route('admin.video-categories.edit', $category) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors duration-200 group" title="{{ __('admin.edit') }}">
                                        <i class="fas fa-edit text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                    </a>
                                    <form action="{{ route('admin.video-categories.destroy', $category) }}" method="POST" class="inline-flex" onsubmit="return confirmDelete('{{ $category->name }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-colors duration-200 group" title="{{ __('admin.delete') }}">
                                            <i class="fas fa-trash text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- 空状态 -->
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('admin.no_categories') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">还没有创建任何分类</p>
                <a href="{{ route('admin.video-categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-accent-600 to-accent-700 text-white font-medium rounded-lg hover:from-accent-700 hover:to-accent-800 focus:outline-none focus:ring-2 focus:ring-accent-600 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2 text-sm"></i>
                    {{ __('admin.create_category') }}
                </a>
            </div>
        @endif
    </div>

    <!-- 分页 -->
    @if($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection