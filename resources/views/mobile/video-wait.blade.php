<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('mobile.video.wait_message') }} - {{ $category->name }}</title>
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
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(2.4);
                opacity: 0;
            }
        }
        .countdown {
            font-family: 'Courier New', monospace;
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="font-sans">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Main Card -->
            <div class="glass rounded-3xl p-8 text-center text-white">
                <!-- Icon with Pulse Animation -->
                <div class="relative mb-8">
                    <div class="pulse-ring absolute inset-0 rounded-full bg-white opacity-20"></div>
                    <div class="relative bg-white bg-opacity-20 rounded-full w-24 h-24 mx-auto flex items-center justify-center">
                        <i class="fas fa-clock text-4xl"></i>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold mb-4">{{ __('mobile.video.wait_message') }}</h1>
                <p class="text-lg opacity-90 mb-8">{{ __('mobile.video.wait_message') }}</p>

                <!-- Countdown Timer -->
                <div class="bg-white bg-opacity-20 rounded-2xl p-6 mb-8">
                    <p class="text-sm opacity-80 mb-2">{{ __('mobile.video.wait_minutes') }}</p>
                    <div id="countdown" class="countdown text-white">
                        <span id="minutes">--</span>:<span id="seconds">--</span>
                    </div>
                </div>

                <!-- Category Info -->
                <div class="bg-white bg-opacity-10 rounded-2xl p-4 mb-6">
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <i class="fas fa-folder"></i>
                        <span class="font-semibold">{{ $category->name }}</span>
                    </div>
                    <p class="text-sm opacity-80">{{ __('mobile.category.select_category') }}</p>
                </div>

                <!-- Refresh Button -->
                <button 
                    id="refreshBtn"
                    onclick="checkAvailability()"
                    class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 py-3 px-6 rounded-2xl font-semibold flex items-center justify-center space-x-2"
                    disabled
                >
                    <i class="fas fa-sync-alt" id="refreshIcon"></i>
                    <span id="refreshText">{{ __('mobile.common.loading') }}</span>
                </button>

                <!-- Tips -->
                <div class="mt-6 text-sm opacity-70">
                    <p><i class="fas fa-info-circle mr-1"></i> {{ __('mobile.video.wait_message') }}</p>
                </div>
            </div>

            <!-- Auto Refresh Notice -->
            <div class="mt-4 text-center">
                <p class="text-white text-sm opacity-80">
                    <i class="fas fa-magic mr-1"></i>
                    {{ __('mobile.common.loading') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // 获取下次可用时间
        const nextAvailableTime = new Date('{{ $nextAvailableTime ? $nextAvailableTime->toISOString() : "" }}');
        const categoryId = {{ $category->id }};
        
        let countdownInterval;
        let autoRefreshTimeout;

        function updateCountdown() {
            const now = new Date();
            const timeDiff = nextAvailableTime - now;

            if (timeDiff <= 0) {
                // 时间到了，可以刷新
                clearInterval(countdownInterval);
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                
                // 启用刷新按钮
                const refreshBtn = document.getElementById('refreshBtn');
                refreshBtn.disabled = false;
                refreshBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                refreshBtn.classList.remove('bg-white', 'bg-opacity-20', 'hover:bg-opacity-30');
                document.getElementById('refreshText').textContent = '立即查看视频';
                
                // 自动刷新
                autoRefreshTimeout = setTimeout(() => {
                    window.location.href = '{{ route("mobile.category.video", $category->id) }}';
                }, 2000);
                
                return;
            }

            const minutes = Math.floor(timeDiff / 60000);
            const seconds = Math.floor((timeDiff % 60000) / 1000);

            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }

        function checkAvailability() {
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');
            const refreshText = document.getElementById('refreshText');
            
            // 显示加载状态
            refreshIcon.classList.add('fa-spin');
            refreshText.textContent = '检查中...';
            refreshBtn.disabled = true;
            
            // 检查是否可以查看视频
            fetch(`/mobile/category/${categoryId}/info`)
                .then(response => response.json())
                .then(data => {
                    if (data.has_available_videos) {
                        // 有可用视频，跳转
                        window.location.href = '{{ route("mobile.category.video", $category->id) }}';
                    } else {
                        // 没有可用视频
                        refreshIcon.classList.remove('fa-spin');
                        refreshText.textContent = '暂无可用视频';
                        setTimeout(() => {
                            refreshText.textContent = '检查可用性';
                            refreshBtn.disabled = false;
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error checking availability:', error);
                    refreshIcon.classList.remove('fa-spin');
                    refreshText.textContent = '检查失败';
                    setTimeout(() => {
                        refreshText.textContent = '检查可用性';
                        refreshBtn.disabled = false;
                    }, 2000);
                });
        }

        // 初始化倒计时
        if (nextAvailableTime && !isNaN(nextAvailableTime.getTime())) {
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
        } else {
            // 如果没有有效的等待时间，直接显示可以查看
            document.getElementById('minutes').textContent = '00';
            document.getElementById('seconds').textContent = '00';
            const refreshBtn = document.getElementById('refreshBtn');
            refreshBtn.disabled = false;
            refreshBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            document.getElementById('refreshText').textContent = '立即查看视频';
        }

        // 页面卸载时清理定时器
        window.addEventListener('beforeunload', function() {
            if (countdownInterval) clearInterval(countdownInterval);
            if (autoRefreshTimeout) clearTimeout(autoRefreshTimeout);
        });
    </script>
</body>
</html>