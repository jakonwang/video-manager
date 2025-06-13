@extends('admin.layouts.app')

@section('title', __('admin.download_logs'))

@section('content')
<div class="space-y-6">
    <!-- 统计卡片 -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="glass-effect rounded-xl p-4 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-xl bg-blue-500/10 p-3">
                    <i class="fas fa-download text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('admin.total_downloads') }}</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-xl p-4 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-xl bg-green-500/10 p-3">
                    <i class="fas fa-check text-green-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('admin.successful_downloads') }}</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['success'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-xl p-4 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-xl bg-red-500/10 p-3">
                    <i class="fas fa-times text-red-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('admin.failed_downloads') }}</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['failed'] }}</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-xl p-4 hover-lift">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-xl bg-purple-500/10 p-3">
                    <i class="fas fa-calendar-day text-purple-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('admin.today_downloads') }}</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['today'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 搜索和过滤 -->
    <div class="glass-effect rounded-xl p-6">
        <form action="{{ route('admin.downloads.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search mr-1.5 text-primary-500 text-xs"></i>
                        {{ __('admin.search') }}
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="mt-1 block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm">
                </div>

                <div>
                    <label for="is_success" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle mr-1.5 text-primary-500 text-xs"></i>
                        {{ __('admin.status') }}
                    </label>
                    <select name="is_success" id="is_success"
                            class="mt-1 block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm">
                        <option value="">{{ __('admin.all') }}</option>
                        <option value="1" {{ request('is_success') === '1' ? 'selected' : '' }}>{{ __('admin.successful') }}</option>
                        <option value="0" {{ request('is_success') === '0' ? 'selected' : '' }}>{{ __('admin.failed') }}</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-calendar mr-1.5 text-primary-500 text-xs"></i>
                        {{ __('admin.start_date') }}
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="mt-1 block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-calendar mr-1.5 text-primary-500 text-xs"></i>
                        {{ __('admin.end_date') }}
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="mt-1 block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm">
                </div>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 border border-transparent rounded-lg text-sm font-medium text-white shadow-md hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                    <i class="fas fa-search mr-1.5 text-xs"></i>
                    {{ __('admin.search') }}
                </button>

                <form action="{{ route('admin.downloads.clear') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg text-sm font-medium text-white shadow-md hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200" onclick="return confirm('{{ __('admin.confirm_clear_downloads') }}')">
                        <i class="fas fa-trash-alt mr-1.5 text-xs"></i>
                        {{ __('admin.clear_old_records') }}
                    </button>
                </form>
            </div>
        </form>
    </div>

    <!-- 下载记录列表 -->
    <div class="glass-effect rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.video') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.ip_address') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.location') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.download_time') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($downloads as $download)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $download->video->title }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $download->ip_address }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $download->country }} {{ $download->city }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $download->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.downloads.show', $download) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.downloads.destroy', $download) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('{{ __('admin.confirm_delete') }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                {{ __('admin.no_records_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
            {{ $downloads->links() }}
        </div>
    </div>
</div>
@endsection 