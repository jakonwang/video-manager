<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 视频管理系统</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(40px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg relative overflow-hidden">
    <!-- 浮动装饰元素 -->
    <div class="floating-shapes">
        <div class="shape w-32 h-32 bg-white rounded-full"></div>
        <div class="shape w-24 h-24 bg-white rounded-full"></div>
        <div class="shape w-40 h-40 bg-white rounded-full"></div>
    </div>
    
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full space-y-8 animate-fade-in">
            <!-- Logo和标题 -->
            <div class="text-center animate-slide-up">
                <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-2xl mb-6">
                    <i class="fas fa-film text-primary-500 text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">
                    视频管理系统
                </h2>
                <p class="text-primary-100 text-lg">
                    欢迎回来，请登录您的账号
                </p>
            </div>
            
            <!-- 登录表单 -->
            <div class="glass-effect rounded-2xl shadow-2xl p-8 space-y-6 animate-slide-up" style="animation-delay: 0.2s">
                <form class="space-y-6" action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    
                    <!-- 邮箱输入 -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-envelope mr-2 text-primary-500"></i>
                            邮箱地址
                        </label>
                        <div class="relative">
                            <input id="email" name="email" type="email" required 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 bg-white/80 backdrop-blur-sm" 
                                   placeholder="请输入您的邮箱地址">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 密码输入 -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-lock mr-2 text-primary-500"></i>
                            密码
                        </label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 bg-white/80 backdrop-blur-sm" 
                                   placeholder="请输入您的密码">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 记住我 -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" 
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded transition-all duration-200">
                            <label for="remember-me" class="ml-3 block text-sm text-gray-700 font-medium">
                                记住我
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-primary-600 hover:text-primary-500 transition-colors duration-200">
                                忘记密码？
                            </a>
                        </div>
                    </div>
                    
                    <!-- 登录按钮 -->
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-primary-300 group-hover:text-primary-200 transition-colors duration-300"></i>
                            </span>
                            <span class="ml-3">立即登录</span>
                        </button>
                    </div>
                    
                    <!-- 错误信息 -->
                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 border border-red-200 p-4 animate-slide-up">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-semibold text-red-800">登录失败</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <p class="mb-1">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
            
            <!-- 底部信息 -->
            <div class="text-center animate-fade-in" style="animation-delay: 0.4s">
                <p class="text-primary-100 text-sm">
                    © 2024 视频管理系统. 保留所有权利.
                </p>
            </div>
        </div>
    </div>
</body>
</html>