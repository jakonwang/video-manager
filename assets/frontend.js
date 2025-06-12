jQuery(document).ready(function($) {
    'use strict';
    
    // 全局变量
    let currentCategory = '';
    let currentVideo = null;
    let isLoading = false;
    let currentLanguage = 'en_US';
    
    // 获取国际化文本
    const i18n = videoManagerFrontend.i18n || {};
    
    // 初始化
    initCategoryDisplay();
    
    // 初始化分类显示
    function initCategoryDisplay() {
        const container = $('.video-category-display');
        if (!container.length) return;
        
        currentCategory = container.data('category');
        currentLanguage = videoManagerFrontend.language || 'en_US';
        
        if (!currentCategory) {
            showError(i18n.missing_category || 'Category parameter missing');
            return;
        }
        
        console.log('Initializing category display:', currentCategory, 'Language:', currentLanguage);
        
        initControls();
        loadCategoryVideo();
    }
    
    // 初始化控件
    function initControls() {
        console.log('=== Initializing Controls ===');
        
        // 预览按钮
        $(document).off('click.preview');
        $(document).on('click.preview', '#preview-video-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('=== Preview Button Clicked ===');
            
            if (currentVideo && currentVideo.url) {
                previewVideo(currentVideo);
            } else {
                console.error('No current video or video URL missing');
            }
        });
        
        // 下载按钮
        $(document).off('click.download').on('click.download', '#download-video-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Download button clicked');
            if (currentVideo && currentVideo.url) {
                showDownloadConfirm(currentVideo);
            }
        });
        
        // 初始化模态框控制
        initModalControls();
        
        console.log('Controls initialization completed');
    }
    
    // 加载分类视频函数，处理冷却时间响应
    function loadCategoryVideo() {
        console.log('Current category:', currentCategory);
        console.log('AJAX URL:', videoManagerFrontend.ajaxUrl);
        console.log('Nonce:', videoManagerFrontend.nonce);
        console.log('Current language:', currentLanguage);
        
        if (isLoading) return;
        
        isLoading = true;
        showLoading();
        
        console.log('Sending AJAX request...');
        
        $.ajax({
            url: `/video-categories/${currentCategory}/videos`,
            type: 'GET',
            dataType: 'json',
            success: function(response, textStatus, jqXHR) {
                console.log('AJAX success response:', response);
                
                // 检查响应格式
                if (typeof response === 'string') {
                    console.error('Response is string instead of JSON:', response);
                    if (response === '-1') {
                        showError(i18n.auth_failed || 'Permission verification failed, please refresh page and try again');
                    } else {
                        showError(i18n.invalid_response || 'Server returned invalid response format');
                    }
                    return;
                }
                
                if (response && response.success) {
                    const data = response.data;
                    console.log('Video data:', data);
                    
                    updateCategoryInfo(data.category);
                    
                    if (data.videos && data.videos.length > 0) {
                        currentVideo = data.videos[0];
                        displaySingleVideo(currentVideo);
                        console.log('Displaying video:', currentVideo);
                    } else {
                        // 检查是否有冷却时间
                        const cooldownTime = data.cooldown_remaining || null;
                        const message = data.message || getLocalizedText('no_video_available', 'No video available at the moment');
                        showNoVideo(message, cooldownTime);
                    }
                    
                    // 显示调试信息（开发环境）
                    if (data.debug_info) {
                        console.log('Debug info:', data.debug_info);
                    }
                } else {
                    console.error('AJAX response failed:', response);
                    showError(response.data || getLocalizedText('load_failed', 'Load failed'));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error details:');
                console.error('Status code:', jqXHR.status);
                console.error('Status text:', textStatus);
                console.error('Error message:', errorThrown);
                console.error('Response text:', jqXHR.responseText);
                
                let errorMessage = getLocalizedText('network_error', 'Network error, please try again later');
                
                if (jqXHR.status === 0) {
                    errorMessage = getLocalizedText('connection_failed', 'Network connection failed, please check network');
                } else if (jqXHR.status === 403) {
                    errorMessage = getLocalizedText('permission_denied', 'Insufficient permissions, please log in again');
                } else if (jqXHR.status === 404) {
                    errorMessage = getLocalizedText('endpoint_not_found', 'AJAX endpoint not found');
                } else if (jqXHR.status === 500) {
                    errorMessage = getLocalizedText('server_error', 'Internal server error');
                } else if (jqXHR.responseText === '-1') {
                    errorMessage = getLocalizedText('wp_security_failed', 'WordPress security verification failed, please refresh page');
                }
                
                showError(errorMessage);
            },
            complete: function() {
                console.log('AJAX request completed');
                isLoading = false;
            }
        });
    }
    
    // 获取本地化文本
    function getLocalizedText(key, fallback) {
        return i18n[key] || fallback;
    }
    
    // 显示单个视频 - 精简版本
    function displaySingleVideo(video) {
        console.log('Displaying single video:', video);
        
        // 使用本地化的视频标题
        const displayTitle = getVideoDisplayTitle(video);
        
        const videoHtml = `
            <div class="video-compact">
                <h3 class="video-title">${escapeHtml(displayTitle)}</h3>
                <div class="video-meta">
                    <span>📏 ${formatFileSize(video.file_size)}</span>
                    <span>🎬 ${getFileExtension(video.filename).toUpperCase()}</span>
                    <span>📅 ${formatDate(video.upload_time)}</span>
                </div>
            </div>
        `;
        
        $('#video-card-single').html(videoHtml);
        $('#single-video-container').show();
        $('#no-video-message').hide();
        
        // 更新状态文本
        if (video.recently_downloaded) {
            $('.status-text').text(`⏰ Video temporarily unavailable, please wait 5 minutes.`);
        } else {
            $('.status-text').text(`✅ Video assigned to you, please download promptly.`);
        }
        
        console.log('Video display completed');
    }
    
    // 获取视频显示标题（支持多语言）
    function getVideoDisplayTitle(video) {
        if (currentLanguage === 'en_US' && video.title_en) {
            return video.title_en;
        }
        return video.title;
    }
    
    // 修改：显示无视频状态 - 支持冷却时间显示
    function showNoVideo(message, cooldownTime = null) {
        console.log('Showing no video state:', message, 'Cooldown:', cooldownTime);
        $('#single-video-container').hide();
        $('#no-video-message').show();
        
        let iconHtml = '📭';
        let titleHtml = 'No Video Available';
        
        // 如果有冷却时间，显示倒计时
        if (cooldownTime && cooldownTime > 0) {
            iconHtml = '⏰';
            titleHtml = 'Please Wait';
            
            // 启动倒计时
            startCooldownTimer(cooldownTime);
        }
        
        $('#no-video-reason').html(`
            <div style="color: #718096; margin-bottom: 16px;">
                <span style="font-size: 1.5rem; margin-right: 8px;">${iconHtml}</span>
                ${message}
            </div>
        `);
        
        // 更新标题
        $('#no-video-message .empty-state h3').text(titleHtml);
        
        $('.status-text').html(`
            <span class="status-icon">${iconHtml}</span>
            ${getLocalizedText('no_video_status', 'No video available at the moment')}
        `);
    }
    
    // 新增：冷却时间倒计时
    function startCooldownTimer(minutes) {
        let remainingSeconds = minutes * 60;
        
        const timer = setInterval(function() {
            const mins = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            
            const timeText = `${mins}:${secs.toString().padStart(2, '0')}`;
            
            // 更新显示
            $('.status-text').html(`
                <span class="status-icon">⏰</span>
                Please wait ${timeText} before accessing another video
            `);
            
            $('#no-video-reason').html(`
                <div style="color: #e53e3e; margin-bottom: 16px; font-weight: 600;">
                    <span style="font-size: 1.5rem; margin-right: 8px;">⏰</span>
                    Cooldown Active
                </div>
                <div style="margin-bottom: 24px; color: #4a5568;">
                    You can access another video in <strong>${timeText}</strong>
                </div>
                <div style="margin-bottom: 16px; color: #718096; font-size: 14px;">
                    This prevents excessive downloads and ensures fair access for all users.
                </div>
            `);
            
            remainingSeconds--;
            
            // 倒计时结束
            if (remainingSeconds < 0) {
                clearInterval(timer);
                console.log('Cooldown finished, reloading videos...');
                loadCategoryVideo();
            }
        }, 1000);
    }
    
    // 精简的下载确认对话框
    function showDownloadConfirm(video) {
        console.log('Showing download confirmation:', video);
        
        const displayTitle = getVideoDisplayTitle(video);
        
        const confirmHtml = `
            <div class="download-confirm-modal" id="download-confirm-modal">
                <div class="modal-overlay"></div>
                <div class="modal-content-compact">
                    <div class="modal-header-compact">
                        <h3>⚠️ Download Confirmation</h3>
                        <button type="button" class="close-btn" id="close-download-modal">&times;</button>
                    </div>
                    <div class="modal-body-compact">
                        <p><strong>Download:</strong> ${escapeHtml(displayTitle)}</p>
                        <p class="warning-compact">This video will be temporarily unavailable after download (10 min cooldown).</p>
                    </div>
                    <div class="modal-footer-compact">
                        <button type="button" class="btn-compact btn-download-compact" id="confirm-download">
                            ⬇️ Download
                        </button>
                        <button type="button" class="btn-compact btn-cancel-compact" id="cancel-download">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // 移除旧的模态框
        $('#download-confirm-modal').remove();
        
        // 添加新的模态框
        $('body').append(confirmHtml);
        
        // 显示模态框
        setTimeout(function() {
            $('#download-confirm-modal').addClass('show');
        }, 50);
        
        // 绑定事件
        $(document).off('click.download-confirm');
        $(document).on('click.download-confirm', '#confirm-download', function(e) {
            e.preventDefault();
            e.stopPropagation();
            downloadAndRecord(video);
        });
        
        $(document).on('click.download-confirm', '#cancel-download, #close-download-modal', function(e) {
            e.preventDefault();
            e.stopPropagation();
            hideDownloadConfirm();
        });
        
        $(document).on('click.download-confirm', '#download-confirm-modal .modal-overlay', function(e) {
            if (e.target === this) {
                e.preventDefault();
                e.stopPropagation();
                hideDownloadConfirm();
            }
        });
    }
    
    // 修复：执行下载并记录 - 添加视频状态更新
    function downloadAndRecord(video) {
        // 隐藏确认框
        hideDownloadConfirm();
        
        // 开始下载
        const a = document.createElement('a');
        a.href = video.url;
        a.download = video.filename;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        // 记录下载并更新视频状态
        $.ajax({
            url: videoManagerFrontend.ajaxUrl,
            type: 'POST',
            data: {
                action: 'record_video_download',
                nonce: videoManagerFrontend.nonce,
                video_id: video.id
            },
            success: function(response) {
                console.log('Download recorded successfully');
                
                // 修复：同时更新视频状态为已使用
                updateVideoStatusToUsed(video.id);
                
                showTemporarilyUnavailable();
            },
            error: function() {
                console.log('Download record failed, but file downloaded');
                
                // 即使记录失败，也尝试更新视频状态
                updateVideoStatusToUsed(video.id);
                
                showTemporarilyUnavailable();
            }
        });
    }
    
    // 新增：更新视频状态为已使用
    function updateVideoStatusToUsed(videoId) {
        $.ajax({
            url: videoManagerFrontend.ajaxUrl,
            type: 'POST',
            data: {
                action: 'update_video_status',
                nonce: videoManagerFrontend.nonce,
                video_id: videoId,
                status: 'used'
            },
            success: function(response) {
                console.log('Video status updated to used successfully');
            },
            error: function() {
                console.log('Failed to update video status to used');
            }
        });
    }
    
    // 修改：显示临时不可用状态，现在改为10分钟
    function showTemporarilyUnavailable() {
        $('.status-text').text(`⏰ ${getLocalizedText('video_temporarily_unavailable', 'Video temporarily unavailable, please wait 10 minutes before trying again.')}`);
        
        // 标记当前视频为最近下载过
        if (currentVideo) {
            currentVideo.recently_downloaded = true;
        }
        
        // 显示冷却提示，但不自动刷新（因为现在是10分钟）
        $('#single-video-container').hide();
        $('#no-video-message').show();
        $('#no-video-reason').html(`
            <div style="color: #e53e3e; margin-bottom: 16px; font-weight: 600;">
                <span style="font-size: 1.5rem; margin-right: 8px;">⏰</span>
                Download Complete
            </div>
            <div style="margin-bottom: 24px; color: #4a5568;">
                Thank you for downloading! You can access another video after 10 minutes.
            </div>
            <div style="margin-bottom: 16px; color: #718096; font-size: 14px;">
                This cooldown period ensures fair access for all users.
            </div>
            <button type="button" class="btn-compact btn-refresh-compact" onclick="location.reload()" style="margin-top: 16px;">
                <span>🔄</span>
                <span>Check Again</span>
            </button>
        `);
        
        $('#no-video-message .empty-state h3').text('Download Complete');
    }
    
    // 隐藏下载确认框
    function hideDownloadConfirm() {
        console.log('Hiding download confirmation');
        $('#download-confirm-modal').removeClass('show');
        // 移除事件委托
        $(document).off('click.download-confirm');
        setTimeout(function() {
            $('#download-confirm-modal').remove();
        }, 300);
    }
    
    // 预览视频功能
    function previewVideo(video) {
        console.log('=== Preview Video Function Called ===');
        console.log('Video object:', video);
        
        if (!video || !video.url) {
            console.error('Video or video URL is missing');
            return;
        }
        
        const displayTitle = getVideoDisplayTitle(video);
        console.log('Display title:', displayTitle);
        
        // 设置模态框内容
        $('#modal-video-title').text(displayTitle);
        $('#modal-filename').text(video.filename);
        $('#modal-filesize').text(formatFileSize(video.file_size));
        $('#modal-upload-time').text(formatDate(video.upload_time));
        
        // 设置视频源
        const videoElement = $('#modal-video')[0];
        const sourceElement = $('#modal-video-source');
        
        if (!videoElement) {
            console.error('Video element not found!');
            return;
        }
        
        console.log('Video element found:', videoElement);
        console.log('Setting video source to:', video.url);
        
        // 清除之前的源
        videoElement.pause();
        videoElement.currentTime = 0;
        
        // 设置视频源
        videoElement.src = video.url;
        sourceElement.attr('src', video.url);
        
        // 重新加载视频
        videoElement.load();
        
        // 监听视频加载事件
        $(videoElement).off('loadstart loadedmetadata canplay error loadeddata').on({
            'loadstart': function() {
                console.log('✅ Video load started');
            },
            'loadedmetadata': function() {
                console.log('✅ Video metadata loaded');
            },
            'loadeddata': function() {
                console.log('✅ Video data loaded');
            },
            'canplay': function() {
                console.log('✅ Video can start playing');
            },
            'error': function(e) {
                console.error('❌ Video load error:', e);
                console.error('Video error details:', videoElement.error);
            }
        });
        
        // 显示模态框
        console.log('Showing modal...');
        showModal('#video-modal');
    }
    
    // 获取文件扩展名
    function getFileExtension(filename) {
        return filename.split('.').pop() || '';
    }
    
    // 更新分类信息
    function updateCategoryInfo(category) {
        const displayName = getCategoryDisplayName(category);
        
        if (displayName) {
            document.title = displayName + ' - Video Category';
        }
    }
    
    // 获取分类显示名称
    function getCategoryDisplayName(category) {
        if (currentLanguage === 'en_US' && category.name_en) {
            return category.name_en;
        }
        return category.name || category.display_name;
    }
    
    // 显示加载状态
    function showLoading() {
        $('.status-text').html(`
            <div class="loading-spinner"></div>
            ${getLocalizedText('assigning_video', 'Assigning video for you...')}
        `);
        $('#single-video-container').hide();
        $('#no-video-message').hide();
    }
    
    // 显示错误信息
    function showError(message) {
        $('#single-video-container').hide();
        $('#no-video-message').show();
        $('#no-video-reason').html(`
            <div style="color: #e53e3e; margin-bottom: 16px; font-weight: 600;">
                <span style="font-size: 1.5rem; margin-right: 8px;">⚠️</span>
                ${getLocalizedText('load_failed', 'Load Failed')}
            </div>
            <div style="margin-bottom: 24px; color: #4a5568;">
                ${escapeHtml(message)}
            </div>
            <button type="button" class="btn-compact btn-refresh-compact" onclick="location.reload()" style="margin-top: 16px;">
                <span>🔄</span>
                <span>${getLocalizedText('reload', 'Reload')}</span>
            </button>
        `);
        
        $('.status-text').html(`
            <span class="status-icon">❌</span>
            ${getLocalizedText('load_failed', 'Load Failed')}
        `);
    }
    
    // 模态框控制
    function initModalControls() {
        console.log('=== Initializing Modal Controls ===');
        
        // 移除所有之前的模态框事件绑定
        $(document).off('click.video-modal');
        $(document).off('keydown.modal');
        
        // 视频预览模态框关闭
        $(document).on('click.video-modal', '#close-modal, #modal-close, .close-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('=== Close Button Clicked ===');
            hideModal('#video-modal');
        });
        
        // 点击遮罩层关闭模态框
        $(document).on('click.video-modal', '.modal .modal-overlay', function(e) {
            if (e.target === this) {
                e.preventDefault();
                e.stopPropagation();
                console.log('=== Overlay Clicked ===');
                hideModal('#video-modal');
            }
        });
        
        // 模态框下载按钮
        $(document).on('click.video-modal', '#modal-download', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Modal download button clicked');
            if (currentVideo) {
                hideModal('#video-modal');
                showDownloadConfirm(currentVideo);
            }
        });
        
        // ESC键关闭模态框
        $(document).on('keydown.modal', function(e) {
            if (e.keyCode === 27) {
                console.log('=== ESC Key Pressed ===');
                hideModal('#video-modal');
                hideDownloadConfirm();
            }
        });
        
        console.log('Modal controls initialized');
    }
    
    // 显示模态框
    function showModal(selector) {
        console.log('=== Show Modal Function Called ===');
        
        const modal = $(selector);
        if (modal.length === 0) {
            console.error('Modal element not found with selector:', selector);
            return;
        }
        
        modal.removeClass('hide').addClass('show').css({
            'display': 'flex',
            'opacity': '1'
        });
        
        $('body').css('overflow', 'hidden');
        console.log('Modal should be visible now');
    }
    
    // 隐藏模态框
    function hideModal(selector) {
        console.log('=== Hide Modal Function Called ===');
        
        const modal = $(selector);
        if (modal.length === 0) {
            console.error('Modal element not found with selector:', selector);
            return;
        }
        
        modal.removeClass('show').css({
            'display': 'none',
            'opacity': '0'
        });
        
        $('body').css('overflow', '');
        
        // 如果是视频模态框，暂停视频
        if (selector === '#video-modal') {
            const video = $('#modal-video')[0];
            if (video) {
                console.log('Pausing and resetting video');
                video.pause();
                video.currentTime = 0;
                video.src = '';
            }
        }
        
        console.log('Modal hidden successfully');
    }
    
    // 工具函数
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 ' + getLocalizedText('bytes', 'Bytes');
        
        const k = 1024;
        const sizes = [
            getLocalizedText('bytes', 'Bytes'),
            getLocalizedText('kb', 'KB'),
            getLocalizedText('mb', 'MB'),
            getLocalizedText('gb', 'GB'),
            getLocalizedText('tb', 'TB')
        ];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) {
            return getLocalizedText('today', 'Today');
        } else if (diffDays === 1) {
            return getLocalizedText('yesterday', 'Yesterday');
        } else if (diffDays < 7) {
            return getLocalizedText('days_ago', '%d days ago').replace('%d', diffDays);
        } else {
            if (currentLanguage === 'en_US') {
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } else {
                return date.toLocaleDateString('zh-CN');
            }
        }
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // 调试帮助函数
    window.videoManagerDebug = {
        getCurrentVideo: function() {
            console.log('Current video:', currentVideo);
            return currentVideo;
        },
        getCurrentLanguage: function() {
            console.log('Current language:', currentLanguage);
            return currentLanguage;
        },
        testPreview: function() {
            console.log('=== Testing Preview Function ===');
            if (currentVideo) {
                previewVideo(currentVideo);
            } else {
                const testVideo = {
                    id: 1,
                    title: 'Test Video',
                    title_en: 'Test Video EN',
                    filename: 'test.mp4',
                    file_size: 1024000,
                    upload_time: '2024-01-01 12:00:00',
                    url: 'https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4'
                };
                previewVideo(testVideo);
            }
        },
        reload: function() {
            console.log('Reloading videos');
            loadCategoryVideo();
        },
        testCooldown: function(minutes) {
            console.log('Testing cooldown timer for', minutes, 'minutes');
            startCooldownTimer(minutes || 1);
        }
    };
    
    // 页面加载完成后的检查
    setTimeout(function() {
        console.log('=== Page Element Check ===');
        console.log('Preview button exists:', $('#preview-video-btn').length > 0);
        console.log('Download button exists:', $('#download-video-btn').length > 0);
        console.log('Video modal exists:', $('#video-modal').length > 0);
        console.log('Current video data:', currentVideo);
        console.log('Debug methods available:', Object.keys(window.videoManagerDebug));
    }, 2000);
});