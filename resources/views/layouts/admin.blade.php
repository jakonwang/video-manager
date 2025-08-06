<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ getSiteName() }} - 后台管理</title>
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- 侧边栏 -->
        <div class="bg-gray-800 text-white w-64 py-6 flex flex-col">
            <div class="px-6 mb-8">
                <h1 class="text-2xl font-bold">{{ getSiteName() }}</h1>
            </div>
            <nav class="flex-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-6 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i> 仪表盘
                </a>
                <a href="{{ route('admin.videos.index') }}" class="block px-6 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.videos.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-video mr-2"></i> 视频管理
                </a>
                <a href="{{ route('admin.video-categories.index') }}" class="block px-6 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.video-categories.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-folder mr-2"></i> 分类管理
                </a>
                <a href="{{ route('admin.downloads.index') }}" class="block px-6 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.downloads.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-download mr-2"></i> 下载记录
                </a>
                <a href="{{ route('admin.users.index') }}" class="block px-6 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-users mr-2"></i> 用户管理
                </a>
                <a href="{{ route('admin.settings') }}" class="block px-6 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-cog mr-2"></i> 系统设置
                </a>
            </nav>
            
            <!-- 用户信息和退出按钮 -->
            <div class="px-6 py-4 border-t border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">管理员</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i> 退出登录
                    </button>
                </form>
            </div>
        </div>

        <!-- 主要内容区 -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- 顶部栏 -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('mobile.category.video', ['categoryId' => 1]) }}" target="_blank" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-mobile-alt mr-1"></i> 移动端
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-globe mr-1"></i> 语言
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('language.switch', 'zh') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">中文</a>
                                    <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">English</a>
                                    <a href="{{ route('language.switch', 'vi') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tiếng Việt</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- 页面内容 -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    @stack('scripts')
</body>
</html>