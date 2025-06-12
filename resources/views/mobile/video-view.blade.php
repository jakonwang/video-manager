<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $video->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4F46E5;
            --secondary-color: #3730A3;
            --accent-color: #818CF8;
            --text-color: #1F2937;
            --bg-color: #F9FAFB;
            --card-bg: #FFFFFF;
            --success-color: #10B981;
            --error-color: #EF4444;
        }

        body {
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .video-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 40;
            background: #000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .video-container.minimized {
            width: 200px;
            height: 112.5px;
            right: auto;
            bottom: 1rem;
            top: auto;
            left: 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .video-player {
            width: 100%;
            aspect-ratio: 16/9;
            background: #000;
            transition: all 0.3s ease;
        }

        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .video-container:hover .video-controls {
            opacity: 1;
        }

        .control-btn {
            color: white;
            padding: 0.75rem;
            border-radius: 50%;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(4px);
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .control-btn:active {
            transform: scale(0.95);
        }

        .content-container {
            margin-top: calc(56.25vw + 1rem);
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .content-container.minimized {
            margin-top: 1rem;
        }

        .info-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .download-btn {
            background: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .download-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .download-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .download-btn:hover::before {
            left: 100%;
        }

        .download-btn:active {
            transform: translateY(0);
        }

        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 50;
            backdrop-filter: blur(8px);
        }

        .loading-spinner {
            width: 3.5rem;
            height: 3.5rem;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .lang-select-custom {
            background: linear-gradient(90deg, #232946e6 0%, #181c2ee6 100%);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 9999px;
            box-shadow: 0 4px 24px 0 rgba(40,40,80,0.18);
            padding: 0.5rem 2.2rem 0.5rem 1.2rem;
            font-size: 1rem;
            font-weight: 600;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            transition: box-shadow 0.2s, border 0.2s;
            backdrop-filter: blur(12px);
            cursor: pointer;
        }
        .lang-select-custom:focus {
            border: 2px solid #6c63ff;
            box-shadow: 0 0 0 2px #6c63ff44;
        }
        .lang-select-custom option {
            background: #232946;
            color: #fff;
        }

        .language-select {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: var(--text-color);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .language-select:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .meta-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: #6B7280;
            font-size: 0.875rem;
            margin: 1rem 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .meta-item:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .progress-bar {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            border-radius: 2px;
            background: rgba(255, 255, 255, 0.2);
            outline: none;
            transition: all 0.2s ease;
        }

        .progress-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .progress-bar::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }

        .minimize-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: white;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 45;
        }

        .minimize-btn:hover {
            background: rgba(0, 0, 0, 0.7);
            transform: scale(1.1);
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            display: none;
            z-index: 100;
            backdrop-filter: blur(8px);
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--accent-color);
            color: white;
            border-radius: 2rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .category-badge i {
            font-size: 1rem;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#181c2e] via-[#232946] to-[#181c2e] flex flex-col items-center justify-center relative overflow-x-hidden">
    <!-- 顶部科技感渐变背景 -->
    <div class="absolute top-0 left-0 w-full h-2/5 z-0">
        <div class="w-full h-full bg-gradient-to-br from-blue-900 via-purple-900 to-pink-900 filter blur-lg scale-110 opacity-80"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#232946]/90 via-[#181c2e]/80 to-transparent"></div>
    </div>
    <!-- 语言切换 -->
    <select id="language-select" class="lang-select-custom fixed top-4 right-4 z-50">
        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
        <option value="zh" {{ app()->getLocale() == 'zh' ? 'selected' : '' }}>中文</option>
        <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
    </select>

    <!-- 主体内容 -->
    <div class="w-full max-w-md flex flex-col items-center justify-center flex-1 mx-auto pt-24 pb-8 relative z-10">
        <!-- 视频播放器卡片 -->
        <div class="w-64 h-64 md:w-80 md:h-80 rounded-3xl shadow-2xl bg-[#232946]/80 backdrop-blur-2xl border border-white/10 flex items-center justify-center mb-8 relative overflow-hidden">
            <video id="videoPlayer" class="w-full h-full object-contain rounded-3xl bg-gradient-to-br from-blue-900 via-purple-900 to-pink-900" controls playsinline>
                <source src="{{ route('mobile.video.preview', $video->id) }}" type="{{ $video->mime_type }}">
                {{ __('mobile.video.file_not_found') }}
            </video>
            <!-- 半透明播放icon装饰 -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-auto transition-opacity duration-500" id="playIconWrapper" style="cursor:pointer;">
                <svg id="playIcon" viewBox="0 0 100 100" width="90" height="90" class="drop-shadow-2xl" style="filter: drop-shadow(0 4px 24px #6c63ff88);">
                    <defs>
                        <linearGradient id="playGradient" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#6c63ff"/>
                            <stop offset="100%" stop-color="#ff6ec4"/>
                        </linearGradient>
                    </defs>
                    <polygon points="30,20 80,50 30,80" fill="url(#playGradient)" opacity="0.85" />
                </svg>
            </div>
        </div>
        <!-- 视频标题 -->
        <h1 class="text-2xl md:text-3xl font-extrabold text-white text-center mb-2 tracking-wide drop-shadow-lg">{{ $video->title }}</h1>
        <!-- 视频描述 -->
        @if($video->description)
            <p class="text-base text-blue-100 text-center mb-8 px-4 max-w-xs">{{ $video->description }}</p>
        @endif
        <!-- 下载按钮 -->
        <button onclick="downloadVideo()" class="w-11/12 max-w-xs py-4 text-lg font-bold rounded-full bg-gradient-to-r from-blue-500 via-purple-600 to-pink-500 text-white shadow-2xl hover:from-blue-600 hover:to-pink-600 transition-all duration-200 flex items-center justify-center gap-3 mb-2 tracking-wider backdrop-blur-lg border-2 border-white/10">
            <i class="fas fa-download animate-bounce"></i>
            {{ __('mobile.video.download') }}
        </button>
    </div>

    <!-- 加载遮罩 -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner mb-4"></div>
            <p class="text-white text-lg">{{ __('mobile.common.loading') }}</p>
        </div>
    </div>

    <!-- Toast 提示 -->
    <div id="toast" class="toast rounded-xl bg-gradient-to-r from-blue-500 to-pink-500 text-white shadow-lg"></div>

    <script>
        // 语言切换
        document.getElementById('language-select').addEventListener('change', function() {
            const lang = this.value;
            const currentUrl = window.location.pathname + window.location.search;
            window.location.href = `{{ url('/language/switch') }}/${lang}?redirect=${encodeURIComponent(currentUrl)}`;
        });

        // 视频播放器控制
        const video = document.getElementById('videoPlayer');
        const toast = document.getElementById('toast');

        function showToast(message, duration = 3000) {
            toast.textContent = message;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, duration);
        }

        // 视频下载
        function downloadVideo() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'flex';
            fetch('{{ route('mobile.video.download', $video->id) }}')
                .then(async response => {
                    const contentType = response.headers.get('Content-Type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        // 检查是否为冷却中
                        if (data.next_download_time) {
                            // 跳转到冷却倒计时页面，带上categoryId和时间参数
                            const url = `/mobile/category/{{ $category->id }}/wait?next=${encodeURIComponent(data.next_download_time)}`;
                            window.location.href = url;
                            return;
                        }
                        throw new Error(data.message || '{{ __('mobile.common.error') }}');
                    }
                    if (!response.ok) {
                        throw new Error('{{ __('mobile.common.error') }}');
                    }
                    return response.blob();
                })
                .then(blob => {
                    if (!blob) return; // 已跳转
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = '{{ $video->title }}.{{ pathinfo($video->path, PATHINFO_EXTENSION) }}';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    a.remove();
                    showToast('{{ __('mobile.video.download_started') }}');
                })
                .catch(error => {
                    showToast(error.message);
                })
                .finally(() => {
                    loadingOverlay.style.display = 'none';
                });
        }

        // 移动端优化
        if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            video.setAttribute('playsinline', 'true');
            video.setAttribute('webkit-playsinline', 'true');
        }

        // 防止右键和长按保存
        video.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // 双击全屏
        video.addEventListener('dblclick', function() {
            if (!document.fullscreenElement) {
                video.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });

        // 点击播放icon控制视频播放/暂停，播放时icon自动淡出，暂停时淡入
        const playIconWrapper = document.getElementById('playIconWrapper');
        const playIcon = document.getElementById('playIcon');
        if (playIconWrapper && playIcon) {
            playIconWrapper.addEventListener('click', function() {
                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            });
            // 播放时icon淡出，暂停时淡入
            video.addEventListener('play', function() {
                playIconWrapper.style.opacity = 0;
            });
            video.addEventListener('pause', function() {
                playIconWrapper.style.opacity = 1;
            });
            // 初始状态：如果自动播放，icon隐藏
            if (!video.paused) {
                playIconWrapper.style.opacity = 0;
            }
        }
    </script>
</body>
</html>