<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" class="h-full" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true',
    currentLang: localStorage.getItem('adminLang') || '<?php echo e(app()->getLocale()); ?>',
    sidebarOpen: true
}" :class="{ 'dark': darkMode, 'bg-gray-100': !darkMode, 'bg-gray-900': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('admin.admin_panel')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        accent: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        }
                    },
                    backdropBlur: {
                        xs: '2px',
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Laravel 翻译函数 JavaScript 版本 -->
    <script>
        // 定义翻译函数
        window.__ = function(key, replace = {}) {
            const translations = {
                'admin.language': '<?php echo e(__('admin.language')); ?>',
                'admin.admin_panel': '<?php echo e(__('admin.admin_panel')); ?>',
                'admin.form_validation_error': '<?php echo e(__('admin.form_validation_error')); ?>',
                'admin.light_mode': '<?php echo e(__('admin.light_mode')); ?>',
                'admin.dark_mode': '<?php echo e(__('admin.dark_mode')); ?>',
                'admin.video_management_system': '<?php echo e(__('admin.video_management_system')); ?>',
                'admin.video_list': '<?php echo e(__('admin.video_list')); ?>',
                'admin.upload_video': '<?php echo e(__('admin.upload_video')); ?>',
                'admin.category_management': '<?php echo e(__('admin.category_management')); ?>',
                'admin.system_settings': '<?php echo e(__('admin.system_settings')); ?>',
                'admin.logout': '<?php echo e(__('admin.logout')); ?>',
                'admin.chinese': '<?php echo e(__('admin.chinese')); ?>',
                'admin.english': '<?php echo e(__('admin.english')); ?>',
                'admin.vietnamese': '<?php echo e(__('admin.vietnamese')); ?>'
            };
            
            let translation = translations[key] || key;
            
            // 替换占位符
            Object.keys(replace).forEach(placeholder => {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            });
            
            return translation;
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
        .fade-enter, .fade-leave-to { opacity: 0; }
        
        /* 自定义滚动条 */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #6b7280;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        /* 渐变背景 */
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }
        .dark .gradient-bg {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        
        /* 玻璃效果 */
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-effect {
            background: rgba(17, 24, 39, 0.8);
            border: 1px solid rgba(75, 85, 99, 0.2);
        }
        
        /* 悬浮效果 */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .dark .hover-lift:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
        
        /* 发光效果 */
        .glow {
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.3);
        }
        .dark .glow {
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.5);
        }
    </style>
</head>
<body class="h-full transition-all duration-300 ease-in-out" :class="{ 'bg-gradient-to-br from-slate-50 to-blue-50': !darkMode, 'bg-gradient-to-br from-gray-900 via-gray-800 to-slate-900': darkMode }">
    <div class="min-h-full">
        <!-- 侧边栏 -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 shadow-xl transform transition-transform duration-300 ease-in-out" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700/50">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-film text-white text-sm"></i>
                        </div>
                    </div>
                    <h1 class="text-lg font-semibold text-white"><?php echo e(getSiteName()); ?></h1>
                </div>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white lg:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- 导航菜单 -->
            <nav class="px-2 py-4 space-y-1">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white'); ?>">
                    <i class="fas fa-home w-5 h-5 mr-3"></i>
                    <span><?php echo e(__('admin.dashboard')); ?></span>
                </a>
                
                <a href="<?php echo e(route('admin.videos.index')); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('admin.videos.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white'); ?>">
                    <i class="fas fa-video w-5 h-5 mr-3"></i>
                    <span><?php echo e(__('admin.video_management')); ?></span>
                </a>

                <a href="<?php echo e(route('admin.video-categories.index')); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('admin.video-categories.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white'); ?>">
                    <i class="fas fa-folder w-5 h-5 mr-3"></i>
                    <span><?php echo e(__('admin.category_management')); ?></span>
                </a>

                <a href="<?php echo e(route('admin.downloads.index')); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('admin.downloads.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white'); ?>">
                    <i class="fas fa-download w-5 h-5 mr-3"></i>
                    <span><?php echo e(__('admin.download_logs')); ?></span>
                </a>

                <a href="<?php echo e(route('admin.users.index')); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('admin.users.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white'); ?>">
                    <i class="fas fa-users w-5 h-5 mr-3"></i>
                    <span><?php echo e(__('admin.user_management')); ?></span>
                </a>

                <a href="<?php echo e(route('admin.settings.index')); ?>" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('admin.settings.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white'); ?>">
                    <i class="fas fa-cog w-5 h-5 mr-3"></i>
                    <span><?php echo e(__('admin.system_settings')); ?></span>
                </a>
            </nav>

            <!-- 用户信息和退出按钮 -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700/50">
                <div class="flex items-center mb-4 px-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <?php if(auth()->guard()->check()): ?>
                            <p class="text-sm font-medium text-white"><?php echo e(auth()->user()->name); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e(__('admin.role_admin')); ?></p>
                        <?php else: ?>
                            <p class="text-sm font-medium text-white"><?php echo e(__('admin.not_logged_in')); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e(__('admin.please_login')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="w-full">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <?php echo e(__('admin.logout')); ?>

                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <?php echo e(__('admin.login')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <!-- 主要内容区 -->
        <div class="lg:pl-64">
            <!-- 顶部导航栏 -->
            <div class="sticky top-0 z-40 flex h-14 shrink-0 items-center gap-x-2 border-b border-white/10 glass-effect px-3 shadow-xl sm:gap-x-4 sm:px-4 lg:px-6 animate-fade-in">
                <button type="button" class="-m-2 p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200 lg:hidden hover:scale-110" @click="mobileMenuOpen = true">
                    <i class="fas fa-bars h-5 w-5"></i>
                </button>

                <!-- 分隔符 -->
                <div class="h-5 w-px bg-gray-200/50 dark:bg-gray-700/50 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-3 self-stretch lg:gap-x-4">
                    <div class="flex flex-1 items-center justify-between">
                        <h1 class="text-lg font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent animate-fade-in ml-2"><?php echo $__env->yieldContent('title', __('admin.admin_panel')); ?></h1>
                        
                        <div class="flex items-center space-x-3">
                            <!-- 语言切换 -->
                            <div class="relative" x-data="{ langOpen: false }">
                                <button @click="langOpen = !langOpen" 
                                        class="flex items-center space-x-2 rounded-xl p-3 text-gray-500 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-gray-800/50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 hover-lift backdrop-blur-sm"
                                        :title="__('admin.language')">
                                    <i class="fas fa-globe text-lg"></i>
                                    <span class="text-sm font-medium" x-text="currentLang === 'zh' ? '中文' : (currentLang === 'vi' ? 'VI' : 'EN')"></span>
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': langOpen }"></i>
                                </button>
                                <div x-show="langOpen" 
                                     @click.away="langOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 z-10 mt-2 w-36 origin-top-right rounded-xl glass-effect py-2 shadow-2xl ring-1 ring-black/5 focus:outline-none">
                                    <a href="<?php echo e(route('language.switch', ['locale' => 'zh'])); ?>" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700/50 transition-all duration-200 rounded-lg mx-2"
                                       :class="{ 'bg-primary-50 dark:bg-gray-700/50 text-primary-600 dark:text-primary-400': currentLang === 'zh' }"
                                       @click="currentLang = 'zh'; localStorage.setItem('adminLang', 'zh')">
                                        <i class="fas fa-check mr-3 text-green-500" x-show="currentLang === 'zh'"></i>
                                        <span class="ml-6" x-show="currentLang !== 'zh'"></span>
                                        <?php echo e(__('admin.chinese')); ?>

                                    </a>
                                    <a href="<?php echo e(route('language.switch', ['locale' => 'en'])); ?>" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700/50 transition-all duration-200 rounded-lg mx-2"
                                       :class="{ 'bg-primary-50 dark:bg-gray-700/50 text-primary-600 dark:text-primary-400': currentLang === 'en' }"
                                       @click="currentLang = 'en'; localStorage.setItem('adminLang', 'en')">
                                        <i class="fas fa-check mr-3 text-green-500" x-show="currentLang === 'en'"></i>
                                        <span class="ml-6" x-show="currentLang !== 'en'"></span>
                                        <?php echo e(__('admin.english')); ?>

                                    </a>
                                    <a href="<?php echo e(route('language.switch', ['locale' => 'vi'])); ?>" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-gray-700/50 transition-all duration-200 rounded-lg mx-2"
                                       :class="{ 'bg-primary-50 dark:bg-gray-700/50 text-primary-600 dark:text-primary-400': currentLang === 'vi' }"
                                       @click="currentLang = 'vi'; localStorage.setItem('adminLang', 'vi')">
                                        <i class="fas fa-check mr-3 text-green-500" x-show="currentLang === 'vi'"></i>
                                        <span class="ml-6" x-show="currentLang !== 'vi'"></span>
                                        <?php echo e(__('admin.vietnamese')); ?>

                                    </a>
                                </div>
                            </div>
                            
                            <!-- 暗黑模式切换 -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                                    class="rounded-xl p-3 text-gray-500 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-gray-800/50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 hover-lift backdrop-blur-sm hover:scale-110"
                                    :title="darkMode ? __('admin.light_mode') : __('admin.dark_mode')">
                                <i class="fas text-lg transition-all duration-300" :class="{ 'fa-sun text-yellow-500': !darkMode, 'fa-moon text-blue-400': darkMode }"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 主要内容 -->
            <main class="py-4 px-3 sm:px-4 lg:px-6 max-w-[1600px] mx-auto" :class="{ 'bg-gradient-to-br from-slate-50 via-white to-blue-50': !darkMode, 'bg-gradient-to-br from-gray-900 via-gray-800 to-slate-900': darkMode }">
                <?php if(session('success')): ?>
                    <div class="mb-4 rounded-xl glass-effect p-4 transform transition-all duration-500 animate-bounce-in" 
                         x-data="{ show: true }" 
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
                         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 transform -translate-y-4 scale-95"
                         x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-green-800 dark:text-green-200">操作成功</h3>
                                <p class="mt-1 text-sm text-green-700 dark:text-green-300"><?php echo e(session('success')); ?></p>
                            </div>
                            <div class="ml-4">
                                <button type="button" @click="show = false" class="inline-flex rounded-xl p-2 text-green-500 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800/30 transition-all duration-200 hover:scale-110">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="mb-4 rounded-xl glass-effect p-4 transform transition-all duration-500 animate-bounce-in" 
                         x-data="{ show: true }" 
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
                         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 transform -translate-y-4 scale-95">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200"><?php echo e(__('admin.form_validation_error')); ?></h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <ul class="list-disc space-y-1 pl-5">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="ml-4">
                                <button type="button" @click="show = false" class="inline-flex rounded-xl p-2 text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800/30 transition-all duration-200 hover:scale-110">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="animate-fade-in">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\phpstudy_pro\WWW\video-manager\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>