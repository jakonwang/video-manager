// assets/admin.js

jQuery(document).ready(function($) {
    'use strict';
    
    // 全局变量
    let uploadQueue = [];
    let isUploading = false;
    let currentPage = 1;
    let totalPages = 1;
    
    // 初始化
    initUploadPage();
    initVideoListPage();
    initCategoriesPage();
    initLogsPage();
    
    // 上传页面初始化
    function initUploadPage() {
        if (!$('#upload-area').length) return;
        
        loadCategories();
        initDragDrop();
        initFileSelector();
        initUploadControls();
    }
    
    // 拖拽上传
    function initDragDrop() {
        const uploadArea = $('#upload-area');
        
        uploadArea.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        uploadArea.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        uploadArea.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            handleFileSelection(files);
        });
    }
    
    // 文件选择器
    function initFileSelector() {
        $('#select-files').on('click', function() {
            $('#video-files').click();
        });
        
        $('#video-files').on('change', function() {
            const files = this.files;
            handleFileSelection(files);
        });
    }
    
    // 处理文件选择
    function handleFileSelection(files) {
        const categoryId = $('#video-category').val();
        
        Array.from(files).forEach(file => {
            if (isVideoFile(file)) {
                const queueItem = {
                    id: generateId(),
                    file: file,
                    title: file.name.replace(/\.[^/.]+$/, ""),
                    categoryId: categoryId,
                    status: 'waiting',
                    progress: 0,
                    uploaded: 0,
                    total: file.size,
                    chunks: Math.ceil(file.size / videoManager.chunkSize)
                };
                
                uploadQueue.push(queueItem);
            } else {
                showNotice(__('admin.file_not_supported_format', {name: file.name}), 'error');
            }
        });
        
        updateQueueDisplay();
        $('#upload-queue').show();
    }
    
    // 检查是否为视频文件
    function isVideoFile(file) {
        const videoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv', 'video/webm'];
        return videoTypes.includes(file.type) || /\.(mp4|avi|mov|wmv|flv|webm)$/i.test(file.name);
    }
    
    // 生成唯一ID
    function generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }
    
    // 更新队列显示
    function updateQueueDisplay() {
        const queueList = $('#queue-list');
        queueList.empty();
        
        uploadQueue.forEach(item => {
            const queueItemHtml = `
                <div class="queue-item" data-id="${item.id}">
                    <div class="queue-item-info">
                        <div class="queue-item-title">${item.title}</div>
                        <div class="queue-item-meta">
                            ${formatFileSize(item.total)} • ${item.chunks} 分片
                        </div>
                    </div>
                    <div class="queue-item-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${item.progress}%"></div>
                        </div>
                        <div class="progress-text">${item.progress}%</div>
                    </div>
                    <div class="queue-item-status">
                        <span class="status-badge status-${item.status}">${getStatusText(item.status)}</span>
                    </div>
                </div>
            `;
            queueList.append(queueItemHtml);
        });
        
        updateSummary();
    }
    
    // 获取状态文本
    function getStatusText(status) {
        const statusMap = {
            'waiting': __('admin.waiting'),
        'uploading': __('admin.uploading'),
        'success': __('admin.completed'),
        'error': __('admin.failed'),
        'paused': __('admin.paused')
        };
        return statusMap[status] || status;
    }
    
    // 更新统计信息
    function updateSummary() {
        const total = uploadQueue.length;
        const completed = uploadQueue.filter(item => item.status === 'success').length;
        const uploading = uploadQueue.filter(item => item.status === 'uploading').length;
        const failed = uploadQueue.filter(item => item.status === 'error').length;
        
        $('#total-files').text(total);
        $('#completed-files').text(completed);
        $('#uploading-files').text(uploading);
        $('#failed-files').text(failed);
        
        // 计算整体进度
        if (total > 0) {
            const totalProgress = uploadQueue.reduce((sum, item) => sum + item.progress, 0);
            const overallProgress = Math.round(totalProgress / total);
            
            $('#overall-progress').css('width', overallProgress + '%');
            $('#overall-progress-text').text(overallProgress + '%');
        }
    }
    
    // 上传控制
    function initUploadControls() {
        $('#start-upload').on('click', startUpload);
        $('#pause-upload').on('click', pauseUpload);
        $('#clear-queue').on('click', clearQueue);
        $('#refresh-categories').on('click', loadCategories);
    }
    
    // 开始上传
    function startUpload() {
        if (uploadQueue.length === 0) {
            showNotice(__('admin.no_files_to_upload'), 'warning');
            return;
        }
        
        isUploading = true;
        $('#start-upload').prop('disabled', true);
        $('#pause-upload').prop('disabled', false);
        
        processUploadQueue();
    }
    
    // 处理上传队列
    async function processUploadQueue() {
        const waitingItems = uploadQueue.filter(item => item.status === 'waiting' || item.status === 'paused');
        
        if (waitingItems.length === 0 || !isUploading) {
            isUploading = false;
            $('#start-upload').prop('disabled', false);
            $('#pause-upload').prop('disabled', true);
            return;
        }
        
        // 并发上传（最多3个文件同时上传）
        const concurrent = Math.min(3, waitingItems.length);
        const promises = [];
        
        for (let i = 0; i < concurrent; i++) {
            promises.push(uploadFile(waitingItems[i]));
        }
        
        await Promise.all(promises);
        
        if (isUploading) {
            processUploadQueue();
        }
    }
    
    // 上传单个文件
    async function uploadFile(item) {
        item.status = 'uploading';
        updateQueueDisplay();
        
        try {
            for (let chunk = 0; chunk < item.chunks; chunk++) {
                if (!isUploading) {
                    item.status = 'paused';
                    updateQueueDisplay();
                    return;
                }
                
                await uploadChunk(item, chunk);
                
                item.progress = Math.round(((chunk + 1) / item.chunks) * 100);
                updateItemProgress(item);
            }
            
            item.status = 'success';
            updateQueueDisplay();
            
        } catch (error) {
            console.error('Upload error:', error);
            item.status = 'error';
            updateQueueDisplay();
            showNotice(__('admin.upload_failed') + ': ' + item.title, 'error');
        }
    }
    
    // 上传分片
    function uploadChunk(item, chunkIndex) {
        return new Promise((resolve, reject) => {
            const start = chunkIndex * videoManager.chunkSize;
            const end = Math.min(start + videoManager.chunkSize, item.file.size);
            const chunk = item.file.slice(start, end);
            
            const formData = new FormData();
            formData.append('action', 'upload_video_chunk');
            formData.append('nonce', videoManager.nonce);
            formData.append('file', chunk);
            formData.append('chunk', chunkIndex);
            formData.append('chunks', item.chunks);
            formData.append('name', item.file.name);
            formData.append('title', item.title);
            formData.append('category_id', item.categoryId);
            
            $.ajax({
                url: videoManager.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 60000,
                success: function(response) {
                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(new Error(response.data || __('admin.upload_failed')));
                    }
                },
                error: function(xhr, status, error) {
                    reject(new Error(error || __('admin.network_error')));
                }
            });
        });
    }
    
    // 更新单个项目进度
    function updateItemProgress(item) {
        const itemEl = $(`.queue-item[data-id="${item.id}"]`);
        itemEl.find('.progress-fill').css('width', item.progress + '%');
        itemEl.find('.progress-text').text(item.progress + '%');
        itemEl.find('.status-badge').removeClass().addClass(`status-badge status-${item.status}`).text(getStatusText(item.status));
        
        updateSummary();
    }
    
    // 暂停上传
    function pauseUpload() {
        isUploading = false;
        $('#start-upload').prop('disabled', false);
        $('#pause-upload').prop('disabled', true);
        
        uploadQueue.forEach(item => {
            if (item.status === 'uploading') {
                item.status = 'paused';
            }
        });
        
        updateQueueDisplay();
    }
    
    // 清空队列
    function clearQueue() {
        if (isUploading) {
            if (!confirm(__('admin.confirm_clear_queue'))) {
                return;
            }
            pauseUpload();
        }
        
        uploadQueue = [];
        updateQueueDisplay();
        $('#upload-queue').hide();
    }
    
    // 加载分类
    function loadCategories(selectId = '#video-category') {
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_categories',
                nonce: videoManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    const select = $(selectId);
                    const currentValue = select.val();
                    
                    select.find('option:not(:first)').remove();
                    
                    response.data.forEach(category => {
                        select.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                    
                    if (currentValue) {
                        select.val(currentValue);
                    }
                }
            }
        });
    }
    
    // 视频列表页面初始化
    function initVideoListPage() {
        if (!$('#videos-table-body').length) return;
        
        loadCategories('#filter-category');
        loadVideos();
        initVideoListControls();
        initPagination();
    }
    
    // 加载视频列表
    function loadVideos(page = 1) {
        const searchTerm = $('#search-videos').val();
        const statusFilter = $('#filter-status').val();
        const categoryFilter = $('#filter-category').val();
        
        $('#videos-table-body').html(`
            <tr>
                <td colspan="9" class="loading-row">
                    <div class="loading-spinner"></div>
                    <span>${__('admin.loading')}</span>
                </td>
            </tr>
        `);
        
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_videos',
                nonce: videoManager.nonce,
                page: page,
                search: searchTerm,
                status: statusFilter,
                category: categoryFilter
            },
            success: function(response) {
                if (response.success) {
                    displayVideos(response.data.videos);
                    updatePagination(response.data.total, response.data.pages, page);
                } else {
                    showNotice(__('admin.load_failed') + ': ' + response.data, 'error');
                }
            },
            error: function() {
                showNotice(__('admin.network_error_retry'), 'error');
            }
        });
    }
    
    // 显示视频列表
    function displayVideos(videos) {
        const tbody = $('#videos-table-body');
        tbody.empty();
        
        if (videos.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px;">
                        <p>${__('admin.no_videos_found')}</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        videos.forEach(video => {
            const statusClass = video.status === 'used' ? 'status-used' : 'status-unused';
            const statusText = video.status === 'used' ? __('admin.used') : __('admin.unused');
        const categoryText = video.category_name || __('admin.no_category');
            
            const row = `
                <tr data-video-id="${video.id}">
                    <td class="check-column">
                        <input type="checkbox" class="video-checkbox" value="${video.id}">
                    </td>
                    <td class="column-thumbnail">
                        <div class="video-thumbnail">
                            <i class="dashicons dashicons-format-video"></i>
                        </div>
                    </td>
                    <td class="column-title">
                        <strong class="video-title">${escapeHtml(video.title)}</strong>
                    </td>
                    <td class="column-filename">${escapeHtml(video.filename)}</td>
                    <td class="column-size">${formatFileSize(video.file_size)}</td>
                    <td class="column-category">${escapeHtml(categoryText)}</td>
                    <td class="column-status">
                        <span class="video-status ${statusClass}">${statusText}</span>
                    </td>
                    <td class="column-upload-time">${formatDate(video.upload_time)}</td>
                    <td class="column-actions">
                        <div class="action-buttons">
                            <a href="#" class="action-btn btn-preview" data-video-id="${video.id}">
                                <i class="dashicons dashicons-visibility"></i> ${__('admin.preview')}
                            </a>
                            <a href="#" class="action-btn btn-download" data-video-id="${video.id}">
                                <i class="dashicons dashicons-download"></i> ${__('admin.download')}
                            </a>
                            <a href="#" class="action-btn btn-delete" data-video-id="${video.id}">
                                <i class="dashicons dashicons-trash"></i> ${__('admin.delete')}
                            </a>
                        </div>
                    </td>
                </tr>
            `;
            
            tbody.append(row);
        });
    }
    
    // 视频列表控制
    function initVideoListControls() {
        // 搜索
        $('#search-btn').on('click', function() {
            currentPage = 1;
            loadVideos(currentPage);
        });
        
        $('#search-videos').on('keypress', function(e) {
            if (e.which === 13) {
                currentPage = 1;
                loadVideos(currentPage);
            }
        });
        
        // 筛选
        $('#filter-btn').on('click', function() {
            currentPage = 1;
            loadVideos(currentPage);
        });
        
        $('#reset-filter').on('click', function() {
            $('#search-videos').val('');
            $('#filter-status').val('');
            $('#filter-category').val('');
            currentPage = 1;
            loadVideos(currentPage);
        });
        
        // 全选
        $('#select-all-videos').on('change', function() {
            $('.video-checkbox').prop('checked', this.checked);
        });
        
        // 批量操作
        $('#apply-bulk').on('click', function() {
            const action = $('#bulk-action').val();
            const selectedVideos = $('.video-checkbox:checked').map(function() {
                return this.value;
            }).get();
            
            if (!action) {
                showNotice(__('admin.please_select_action'), 'warning');
                return;
            }
            
            if (selectedVideos.length === 0) {
                showNotice(__('admin.please_select_videos'), 'warning');
                return;
            }
            
            if (action === 'delete') {
                if (!confirm(__('admin.confirm_delete_selected', {count: selectedVideos.length}))) {
                    return;
                }
            }
            
            performBulkAction(action, selectedVideos);
        });
        
        // 视频操作事件委托
        $(document).on('click', '.btn-preview', function(e) {
            e.preventDefault();
            const videoId = $(this).data('video-id');
            previewVideo(videoId);
        });
        
        $(document).on('click', '.btn-download', function(e) {
            e.preventDefault();
            const videoId = $(this).data('video-id');
            downloadVideo(videoId);
        });
        
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const videoId = $(this).data('video-id');
            if (confirm(__('admin.confirm_delete_video'))) {
                deleteVideo(videoId);
            }
        });
    }
    
    // 执行批量操作
    function performBulkAction(action, videoIds) {
        const actionMap = {
            'mark-used': 'used',
            'mark-unused': 'unused'
        };
        
        if (action === 'delete') {
            // 批量删除
            let completed = 0;
            const total = videoIds.length;
            
            videoIds.forEach(videoId => {
                deleteVideo(videoId, false).then(() => {
                    completed++;
                    if (completed === total) {
                        loadVideos(currentPage);
                        showNotice(__('admin.delete_success', {count: total}), 'success');
                    }
                });
            });
        } else {
            // 批量更新状态
            const status = actionMap[action];
            updateVideoStatus(videoIds, status);
        }
    }
    
    // 更新视频状态
    function updateVideoStatus(videoIds, status) {
        const promises = videoIds.map(videoId => {
            return $.ajax({
                url: videoManager.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'update_video_status',
                    nonce: videoManager.nonce,
                    video_id: videoId,
                    status: status
                }
            });
        });
        
        Promise.all(promises).then(responses => {
            const successCount = responses.filter(r => r.success).length;
            if (successCount > 0) {
                loadVideos(currentPage);
                showNotice(__('admin.update_status_success', {count: successCount}), 'success');
            }
        });
    }
    
    // 预览视频
    function previewVideo(videoId) {
        // 获取视频信息
        const row = $(`tr[data-video-id="${videoId}"]`);
        const title = row.find('.video-title').text();
        const filename = row.find('.column-filename').text();
        const filesize = row.find('.column-size').text();
        const uploadTime = row.find('.column-upload-time').text();
        
        // 设置模态框内容
        $('#modal-title').text(title);
        $('#modal-filename').text(filename);
        $('#modal-filesize').text(filesize);
        $('#modal-upload-time').text(uploadTime);
        
        // 设置视频源
        const videoUrl = getVideoUrl(filename);
        $('#preview-video').attr('src', videoUrl);
        
        // 显示模态框
        showModal('#video-preview-modal');
    }
    
    // 下载视频
    function downloadVideo(videoId) {
        const row = $(`tr[data-video-id="${videoId}"]`);
        const filename = row.find('.column-filename').text();
        const videoUrl = getVideoUrl(filename);
        
        // 创建下载链接
        const a = document.createElement('a');
        a.href = videoUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    // 删除视频
    function deleteVideo(videoId, showMessage = true) {
        return $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'delete_video',
                nonce: videoManager.nonce,
                video_id: videoId
            }
        }).then(response => {
            if (response.success) {
                if (showMessage) {
                    showNotice(__('admin.video_delete_success'), 'success');
                    loadVideos(currentPage);
                }
                return true;
            } else {
                if (showMessage) {
                    showNotice(__('admin.delete_failed') + ': ' + response.data, 'error');
                }
                return false;
            }
        });
    }
    
    // 获取视频URL
    function getVideoUrl(filename) {
        return window.location.origin + '/wp-content/uploads/video-manager/' + filename;
    }
    
    // 分页控制
    function initPagination() {
        $('#first-page').on('click', function() {
            if (currentPage > 1) {
                currentPage = 1;
                loadVideos(currentPage);
            }
        });
        
        $('#prev-page').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                loadVideos(currentPage);
            }
        });
        
        $('#next-page').on('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                loadVideos(currentPage);
            }
        });
        
        $('#last-page').on('click', function() {
            if (currentPage < totalPages) {
                currentPage = totalPages;
                loadVideos(currentPage);
            }
        });
        
        $('#current-page').on('change', function() {
            const page = parseInt($(this).val());
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                currentPage = page;
                loadVideos(currentPage);
            }
        });
    }
    
    // 更新分页信息
    function updatePagination(total, pages, current) {
        totalPages = pages;
        currentPage = current;
        
        $('#displaying-num').text(__('admin.total_items', {count: total}));
        $('#total-pages').text(totalPages);
        $('#current-page').val(currentPage);
        
        // 更新按钮状态
        $('#first-page, #prev-page').prop('disabled', currentPage <= 1);
        $('#next-page, #last-page').prop('disabled', currentPage >= totalPages);
    }
    
    // 分类管理页面初始化
    function initCategoriesPage() {
        if (!$('#add-category-form').length) return;
        
        loadCategoriesGrid();
        initCategoryForm();
        initCategoryActions();
    }
    
    // 加载分类网格
    function loadCategoriesGrid() {
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_categories',
                nonce: videoManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayCategories(response.data);
                }
            }
        });
    }
    
    // 显示分类
    function displayCategories(categories) {
        const grid = $('#categories-grid');
        grid.empty();
        
        if (categories.length === 0) {
            grid.html(`
                <div class="empty-category" style="text-align: center; padding: 60px 20px; color: #646970;">
                    <h3>${__('admin.no_categories')}</h3>
            <p>${__('admin.create_first_category')}</p>
                </div>
            `);
            return;
        }
        
        categories.forEach(category => {
            const videoCount = category.video_count || 0;
            const card = `
                <div class="category-card" data-category-id="${category.id}">
                    <div class="category-card-header">
                        <h3 class="category-name">${escapeHtml(category.name)}</h3>
                        <span class="category-slug">${escapeHtml(category.slug)}</span>
                    </div>
                    
                    <div class="category-description">
                        ${escapeHtml(category.description || __('admin.no_description'))}
                    </div>
                    
                    <div class="category-meta">
                        <span class="created-time">${formatDate(category.created_time)}</span>
                        <span class="video-count">${__('admin.video_count', {count: videoCount})}</span>
                    </div>
                    
                    <div class="category-actions">
                        <a href="${window.location.origin}/video-category/${category.slug}" class="category-link" target="_blank">
                            <i class="dashicons dashicons-external"></i> ${__('admin.view_page')}
                        </a>
                        
                        <div class="category-buttons">
                            <button type="button" class="btn-sm btn-edit" data-category-id="${category.id}">
                                <i class="dashicons dashicons-edit"></i> ${__('admin.edit')}
                            </button>
                            <button type="button" class="btn-sm btn-delete" data-category-id="${category.id}">
                                <i class="dashicons dashicons-trash"></i> ${__('admin.delete')}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            grid.append(card);
        });
    }
    
    // 分类表单处理
    function initCategoryForm() {
        $('#add-category-form').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                action: 'create_category',
                nonce: videoManager.nonce,
                name: $('#category-name').val().trim(),
                slug: $('#category-slug').val().trim(),
                description: $('#category-description').val().trim()
            };
            
            if (!formData.name) {
                showNotice(__('admin.please_enter_category_name'), 'warning');
                return;
            }
            
            $.ajax({
                url: videoManager.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showNotice(__('admin.category_create_success'), 'success');
                        $('#add-category-form')[0].reset();
                        loadCategoriesGrid();
                        loadCategories(); // 刷新其他页面的分类选择器
                    } else {
                        showNotice(__('admin.create_failed') + ': ' + response.data, 'error');
                    }
                }
            });
        });
        
        // 自动生成别名
        $('#category-name').on('input', function() {
            if (!$('#category-slug').val()) {
                const slug = $(this).val().toLowerCase()
                    .replace(/[^a-z0-9\u4e00-\u9fa5]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#category-slug').val(slug);
            }
        });
        
        $('#refresh-categories').on('click', loadCategoriesGrid);
    }
    
    // 分类操作
    function initCategoryActions() {
        // 编辑分类
        $(document).on('click', '.btn-edit', function() {
            const categoryId = $(this).data('category-id');
            const card = $(this).closest('.category-card');
            
            // 填充编辑表单
            $('#edit-category-id').val(categoryId);
            $('#edit-category-name').val(card.find('.category-name').text());
            $('#edit-category-slug').val(card.find('.category-slug').text());
            $('#edit-category-description').val(card.find('.category-description').text());
            
            showModal('#edit-category-modal');
        });
        
        // 删除分类
        $(document).on('click', '.btn-delete', function() {
            const categoryId = $(this).data('category-id');
            const categoryName = $(this).closest('.category-card').find('.category-name').text();
            
            $('#delete-category-name').text(categoryName);
            $('#confirm-delete').data('category-id', categoryId);
            
            showModal('#delete-category-modal');
        });
        
        // 查看分类页面 - 移除这个事件处理，因为现在直接用href
        // $(document).on('click', '.category-link', function(e) {
        //     e.preventDefault();
        //     const slug = $(this).data('slug');
        //     const url = window.location.origin + '/?video_category=' + slug;
        //     window.open(url, '_blank');
        // });
        
        // 保存编辑
        $('#save-category').on('click', function() {
            const formData = {
                action: 'update_category',
                nonce: videoManager.nonce,
                category_id: $('#edit-category-id').val(),
                name: $('#edit-category-name').val().trim(),
                slug: $('#edit-category-slug').val().trim(),
                description: $('#edit-category-description').val().trim()
            };
            
            if (!formData.name) {
                showNotice(__('admin.please_enter_category_name'), 'warning');
                return;
            }
            
            // 这里需要添加更新分类的AJAX处理
            hideModal('#edit-category-modal');
            showNotice(__('admin.feature_in_development'), 'info');
        });
        
        // 确认删除
        $('#confirm-delete').on('click', function() {
            const categoryId = $(this).data('category-id');
            
            $.ajax({
                url: videoManager.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'delete_category',
                    nonce: videoManager.nonce,
                    category_id: categoryId
                },
                success: function(response) {
                    if (response.success) {
                        showNotice(__('admin.category_delete_success'), 'success');
                        hideModal('#delete-category-modal');
                        loadCategoriesGrid();
                        loadCategories(); // 刷新其他页面的分类选择器
                    } else {
                        showNotice(__('admin.delete_failed') + ': ' + response.data, 'error');
                    }
                }
            });
        });
    }
    
    // 日志页面初始化
    function initLogsPage() {
        if (!$('#logs-table-body').length) return;
        
        loadLogs();
        initLogControls();
        loadLogStats();
    }
    
    // 加载日志
    function loadLogs(page = 1) {
        const status = $('#filter-log-status').val();
        const action = $('#filter-log-action').val();
        const startDate = $('#filter-date-start').val();
        const endDate = $('#filter-date-end').val();
        
        $('#logs-table-body').html(`
            <tr>
                <td colspan="4" class="loading-row">
                    <div class="loading-spinner"></div>
                    <span>${__('admin.loading_logs')}</span>
                </td>
            </tr>
        `);
        
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_logs',
                nonce: videoManager.nonce,
                page: page,
                status: status,
                log_action: action,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    displayLogs(response.data.logs);
                    updateLogPagination(response.data.total, response.data.pages, page);
                }
            }
        });
    }
    
    // 显示日志
    function displayLogs(logs) {
        const tbody = $('#logs-table-body');
        tbody.empty();
        
        if (logs.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px;">
                        <p>${__('admin.no_logs_found')}</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        logs.forEach(log => {
            const row = `
                <tr data-log-id="${log.id}">
                    <td class="column-status">
                        <span class="log-status ${log.status}"></span>
                    </td>
                    <td class="column-action">
                        <span class="action-type action-${log.action}">${log.action}</span>
                    </td>
                    <td class="column-message">
                        <span class="log-message" data-log-id="${log.id}">${escapeHtml(log.message)}</span>
                    </td>
                    <td class="column-time">${formatDate(log.created_time)}</td>
                </tr>
            `;
            
            tbody.append(row);
        });
    }
    
    // 日志控制
    function initLogControls() {
        $('#filter-logs').on('click', function() {
            loadLogs(1);
        });
        
        $('#reset-log-filter').on('click', function() {
            $('#filter-log-status').val('');
            $('#filter-log-action').val('');
            $('#filter-date-start').val('');
            $('#filter-date-end').val('');
            loadLogs(1);
        });
        
        $('#export-logs').on('click', function() {
            showNotice(__('admin.export_feature_in_development'), 'info');
        });
        
        $('#clear-logs').on('click', function() {
            if (confirm(__('admin.confirm_clear_logs'))) {
        showNotice(__('admin.clear_feature_in_development'), 'info');
            }
        });
        
        // 日志详情
        $(document).on('click', '.log-message', function() {
            const logId = $(this).data('log-id');
            showLogDetail(logId);
        });
        
        // 日志分页控制
        initLogPagination();
    }
    
    // 日志分页控制
    function initLogPagination() {
        $('#logs-first-page').on('click', function() {
            if (!$(this).prop('disabled')) {
                loadLogs(1);
            }
        });
        
        $('#logs-prev-page').on('click', function() {
            if (!$(this).prop('disabled')) {
                const currentPage = parseInt($('#logs-current-page').val());
                if (currentPage > 1) {
                    loadLogs(currentPage - 1);
                }
            }
        });
        
        $('#logs-next-page').on('click', function() {
            if (!$(this).prop('disabled')) {
                const currentPage = parseInt($('#logs-current-page').val());
                const totalPages = parseInt($('#logs-total-pages').text());
                if (currentPage < totalPages) {
                    loadLogs(currentPage + 1);
                }
            }
        });
        
        $('#logs-last-page').on('click', function() {
            if (!$(this).prop('disabled')) {
                const totalPages = parseInt($('#logs-total-pages').text());
                loadLogs(totalPages);
            }
        });
        
        $('#logs-current-page').on('change', function() {
            const page = parseInt($(this).val());
            const totalPages = parseInt($('#logs-total-pages').text());
            if (page >= 1 && page <= totalPages) {
                loadLogs(page);
            }
        });
    }
    
    // 加载日志统计
    function loadLogStats() {
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_log_stats',
                nonce: videoManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    const stats = response.data;
                    $('#success-count').text(stats.success || 0);
                    $('#error-count').text(stats.error || 0);
                    $('#total-logs').text(stats.total || 0);
                    $('#today-logs').text(stats.today || 0);
                }
            }
        });
    }
    
    // 显示日志详情
    function showLogDetail(logId) {
        const row = $(`tr[data-log-id="${logId}"]`);
        const status = row.find('.log-status').hasClass('success') ? 'success' : 'error';
        const action = row.find('.action-type').text();
        const message = row.find('.log-message').text();
        const time = row.find('.column-time').text();
        
        $('#detail-status').removeClass().addClass('status-badge').addClass('status-' + status).text(status);
        $('#detail-action').text(action);
        $('#detail-message').text(message);
        $('#detail-time').text(time);
        
        showModal('#log-detail-modal');
    }
    
    // 更新日志分页
    function updateLogPagination(total, pages, current) {
        $('#logs-displaying-num').text(__('admin.total_items', {count: total}));
        $('#logs-total-pages').text(pages);
        $('#logs-current-page').val(current);
        
        $('#logs-first-page, #logs-prev-page').prop('disabled', current <= 1);
        $('#logs-next-page, #logs-last-page').prop('disabled', current >= pages);
    }
    
    // 模态框控制
    function showModal(selector) {
        $(selector).addClass('show').css('display', 'flex');
        $('body').addClass('modal-open');
    }
    
    function hideModal(selector) {
        $(selector).removeClass('show');
        setTimeout(() => {
            $(selector).css('display', 'none');
        }, 300);
        $('body').removeClass('modal-open');
    }
    
    // 模态框事件
    $(document).on('click', '.close-modal, .modal', function(e) {
        if (e.target === this) {
            hideModal($(this).closest('.modal'));
        }
    });
    
    $(document).on('click', '#close-modal, #cancel-edit, #cancel-delete, #close-log-detail', function() {
        hideModal($(this).closest('.modal'));
    });
    
    // ESC键关闭模态框
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            $('.modal.show').each(function() {
                hideModal($(this));
            });
        }
    });
    
    // 视频预览模态框
    $('#download-video').on('click', function() {
        const videoSrc = $('#preview-video').attr('src');
        const filename = $('#modal-filename').text();
        
        const a = document.createElement('a');
        a.href = videoSrc;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
    
    // 工具函数
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('zh-CN');
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function showNotice(message, type = 'info') {
        // 创建通知元素
        const notice = $(`
            <div class="notice notice-${type} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">${__('admin.dismiss_notice')}</span>
                </button>
            </div>
        `);
        
        // 添加到页面
        $('.wrap').prepend(notice);
        
        // 自动隐藏
        setTimeout(() => {
            notice.fadeOut(() => notice.remove());
        }, 5000);
        
        // 点击关闭
        notice.find('.notice-dismiss').on('click', function() {
            notice.fadeOut(() => notice.remove());
        });
    }
    
    // 防止表单重复提交
    function preventDoubleSubmit() {
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"], input[type="submit"]');
            submitBtn.prop('disabled', true);
            
            setTimeout(() => {
                submitBtn.prop('disabled', false);
            }, 3000);
        });
    }
    
    // 初始化防重复提交
    preventDoubleSubmit();
    
});