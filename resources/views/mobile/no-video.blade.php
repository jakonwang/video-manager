<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>暂无视频 - {{ $category->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="font-sans">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Main Card -->
            <div class="glass rounded-3xl p-8 text-center text-white fade-in">
                <!-- Floating Icon -->
                <div class="float-animation mb-8">
                    <div class="bg-white bg-opacity-20 rounded-full w-24 h-24 mx-auto flex items-center justify-center">
                        <i class="fas fa-video-slash text-4xl"></i>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold mb-4">暂无可用视频</h1>
                <p class="text-lg opacity-90 mb-8">该分类中的所有视频都已被使用</p>

                <!-- Category Info -->
                <div class="bg-white bg-opacity-20 rounded-2xl p-6 mb-8">
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <i class="fas fa-folder"></i>
                        <span class="font-semibold text-lg">{{ $category->name }}</span>
                    </div>
                    @if($category->description)
                    <p class="text-sm opacity-80">{{ $category->description }}</p>
                    @endif
                </div>

                <!-- Status Info -->
                <div class="bg-white bg-opacity-10 rounded-2xl p-4 mb-6">
                    <div class="flex items-center justify-center space-x-2 text-yellow-300">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="font-semibold">所有视频已使用完毕</span>
                    </div>
                    <p class="text-sm opacity-80 mt-2">请联系管理员添加更多视频</p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button 
                        onclick="refreshPage()"
                        class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 py-3 px-6 rounded-2xl font-semibold flex items-center justify-center space-x-2"
                    >
                        <i class="fas fa-sync-alt" id="refreshIcon"></i>
                        <span>刷新检查</span>
                    </button>
                    
                    <button 
                        onclick="goBack()"
                        class="w-full bg-gray-500 bg-opacity-50 hover:bg-opacity-70 transition-all duration-300 py-3 px-6 rounded-2xl font-semibold flex items-center justify-center space-x-2"
                    >
                        <i class="fas fa-arrow-left"></i>
                        <span>返回上页</span>
                    </button>
                </div>

                <!-- Tips -->
                <div class="mt-8 text-sm opacity-70">
                    <div class="bg-white bg-opacity-5 rounded-xl p-4">
                        <p class="mb-2"><i class="fas fa-lightbulb mr-1"></i> <strong>提示：</strong></p>
                        <ul class="text-left space-y-1">
                            <li>• 视频下载后会标记为已使用</li>
                            <li>• 每个IP地址10分钟内只能查看一个视频</li>
                            <li>• 管理员会定期添加新视频</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Auto Refresh Notice -->
            <div class="mt-4 text-center">
                <p class="text-white text-sm opacity-80">
                    <i class="fas fa-clock mr-1"></i>
                    页面将每30秒自动检查一次
                </p>
            </div>
        </div>
    </div>

    <script>
        const categoryId = {{ $category->id }};
        let autoRefreshInterval;

        function refreshPage() {
            const refreshIcon = document.getElementById('refreshIcon');
            refreshIcon.classList.add('fa-spin');
            
            // 检查是否有新的可用视频
            fetch(`/mobile/category/${categoryId}/info`)
                .then(response => response.json())
                .then(data => {
                    if (data.has_available_videos) {
                        // 有可用视频，跳转到视频页面
                        window.location.href = `/mobile/category/${categoryId}/video`;
                    } else {
                        // 仍然没有可用视频
                        refreshIcon.classList.remove('fa-spin');
                        
                        // 显示刷新完成的反馈
                        const originalText = refreshIcon.nextElementSibling.textContent;
                        refreshIcon.nextElementSibling.textContent = '仍无可用视频';
                        
                        setTimeout(() => {
                            refreshIcon.nextElementSibling.textContent = originalText;
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error checking for videos:', error);
                    refreshIcon.classList.remove('fa-spin');
                    
                    const originalText = refreshIcon.nextElementSibling.textContent;
                    refreshIcon.nextElementSibling.textContent = '检查失败';
                    
                    setTimeout(() => {
                        refreshIcon.nextElementSibling.textContent = originalText;
                    }, 2000);
                });
        }

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                // 如果没有历史记录，跳转到首页或其他默认页面
                window.location.href = '/';
            }
        }

        // 自动刷新检查
        function startAutoRefresh() {
            autoRefreshInterval = setInterval(() => {
                fetch(`/mobile/category/${categoryId}/info`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_available_videos) {
                            // 有可用视频，自动跳转
                            clearInterval(autoRefreshInterval);
                            window.location.href = `/mobile/category/${categoryId}/video`;
                        }
                    })
                    .catch(error => {
                        console.error('Auto refresh error:', error);
                    });
            }, 30000); // 每30秒检查一次
        }

        // 页面加载完成后开始自动刷新
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
        });

        // 页面卸载时清理定时器
        window.addEventListener('beforeunload', function() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        });

        // 页面可见性变化时的处理
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // 页面隐藏时停止自动刷新
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
            } else {
                // 页面显示时重新开始自动刷新
                startAutoRefresh();
            }
        });
    </script>
</body>
</html>