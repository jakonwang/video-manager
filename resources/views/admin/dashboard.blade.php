@extends('admin.layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- 总视频数 -->
        <div class="glass-effect rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-video text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.total_videos') }}</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_videos'] }}</p>
                </div>
            </div>
        </div>

        <!-- 已处理视频数 -->
        <div class="glass-effect rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.processed_videos') }}</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['processed_videos'] }}</p>
                </div>
            </div>
        </div>

        <!-- 分类数量 -->
        <div class="glass-effect rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-folder text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.total_categories') }}</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_categories'] }}</p>
                </div>
            </div>
        </div>

        <!-- 已使用视频数 -->
        <div class="glass-effect rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.used_videos') }}</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['used_videos'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- 分类统计 TOP5 -->
        <div class="glass-effect rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-pie mr-2"></i>{{ __('admin.category_stats') }}
            </h3>
            <div class="space-y-4">
                @forelse($categoryStats as $category)
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                        <i class="fas fa-folder text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->videos_count }} {{ __('admin.videos') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mr-2">
                                    <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ ($category->videos_count / $stats['total_videos']) * 100 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format(($category->videos_count / $stats['total_videos']) * 100, 1) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        {{ __('admin.no_categories') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 最近上传的视频 -->
        <div class="glass-effect rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="fas fa-clock mr-2"></i>{{ __('admin.recent_videos') }}
            </h3>
            <div class="space-y-4">
                @forelse($recentVideos as $video)
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                        <i class="fas fa-video text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $video->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $video->category->name ?? __('admin.uncategorized') }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $video->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        {{ __('admin.no_recent_videos') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 近30天上传/下载趋势 -->
    <div class="glass-effect rounded-xl p-6 shadow-md">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            <i class="fas fa-chart-line mr-2"></i>{{ __('admin.upload_download_trend') }}
        </h3>
        <canvas id="trendChart" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = {!! json_encode(array_values(array_unique(array_merge($dailyUploads->keys()->toArray(), $dailyDownloads->keys()->toArray()))) ) !!};
    const uploadData = {!! json_encode($dailyUploads->toArray()) !!};
    const downloadData = {!! json_encode($dailyDownloads->toArray()) !!};

    // 补全数据（没有的日期补0）
    const allDates = labels.sort();
    const uploads = allDates.map(date => uploadData[date] ?? 0);
    const downloads = allDates.map(date => downloadData[date] ?? 0);

    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: allDates,
            datasets: [
                {
                    label: '{{ __("admin.upload_count") }}',
                    data: uploads,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: '{{ __("admin.download_count") }}',
                    data: downloads,
                    borderColor: '#f59e42',
                    backgroundColor: 'rgba(245,158,66,0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                x: { 
                    title: { display: true, text: '{{ __("admin.date") }}' },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                },
                y: { 
                    title: { display: true, text: '{{ __("admin.count") }}' },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                }
            }
        }
    });
});
</script>
@endpush 