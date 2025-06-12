@extends('admin.layouts.app')

@section('title', __('admin.download_details'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- 页面标题 -->
    <div class="glass-effect rounded-xl p-4 shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                    <i class="fas fa-download text-white text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('admin.download_details') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.download_details_description') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.downloads.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gradient-to-r from-gray-500 to-gray-600 text-white hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200 shadow-sm text-sm">
                <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
                {{ __('admin.back_to_list') }}
            </a>
        </div>
    </div>

    <!-- 视频信息 -->
    <div class="glass-effect rounded-xl p-6 shadow-md hover-lift">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-film mr-2 text-primary-500"></i>
            {{ __('admin.video_information') }}
        </h2>
        <div class="flex items-start space-x-4">
            <div class="w-48 h-27 flex-shrink-0">
                <img src="{{ $download->video->thumbnail_url }}" alt="{{ $download->video->title }}" class="w-full h-full object-cover rounded-lg shadow-md">
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $download->video->title }}</h3>
                <div class="mt-2 space-y-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-clock mr-1.5 text-primary-500"></i>
                        {{ __('admin.duration') }}: {{ $download->video->duration }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-file-alt mr-1.5 text-primary-500"></i>
                        {{ __('admin.file_size') }}: {{ formatBytes($download->video->size) }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-folder mr-1.5 text-primary-500"></i>
                        {{ __('admin.category') }}: {{ $download->video->category->name }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 下载信息 -->
    <div class="glass-effect rounded-xl p-6 shadow-md hover-lift">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-primary-500"></i>
            {{ __('admin.download_information') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.download_time') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $download->created_at->format('Y-m-d H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.status') }}</dt>
                        <dd class="mt-1">
                            @if($download->is_success)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ __('admin.successful') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    {{ __('admin.failed') }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.error_message') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $download->error_message ?: '-' }}</dd>
                    </div>
                </dl>
            </div>
            <div>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.ip_address') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $download->ip_address }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.location') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $download->country }} {{ $download->city }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.user_agent') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white break-all">{{ $download->user_agent }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- 操作按钮 -->
    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.downloads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
            {{ __('admin.back') }}
        </a>

        <form action="{{ route('admin.downloads.destroy', $download) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg text-sm font-medium text-white shadow-md hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200" onclick="return confirm('{{ __('admin.confirm_delete') }}')">
                <i class="fas fa-trash-alt mr-1.5 text-xs"></i>
                {{ __('admin.delete_record') }}
            </button>
        </form>
    </div>
</div>
@endsection 