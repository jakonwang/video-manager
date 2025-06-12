<?php
// templates/admin-upload.php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap video-manager-wrap">
    <h1><i class="dashicons dashicons-video-alt3"></i> 视频批量上传</h1>
    
    <div class="video-manager-container">
        <div class="upload-section">
            <div class="upload-header">
                <h2>选择视频文件</h2>
                <p class="description">支持 MP4, AVI, MOV, WMV 等格式，支持断点续传和队列上传</p>
            </div>
            
            <div class="upload-form">
                <div class="form-row">
                    <label for="video-category">选择分类：</label>
                    <select id="video-category" name="category_id">
                        <option value="0">无分类</option>
                    </select>
                    <button type="button" id="refresh-categories" class="button">刷新分类</button>
                </div>
                
                <div class="upload-area" id="upload-area">
                    <div class="upload-icon">
                        <i class="dashicons dashicons-cloud-upload"></i>
                    </div>
                    <h3>拖拽视频文件到这里或点击选择文件</h3>
                    <p>支持多文件同时上传，单文件最大 <?php echo size_format(wp_max_upload_size()); ?></p>
                    <input type="file" id="video-files" multiple accept="video/*" style="display: none;">
                    <button type="button" id="select-files" class="button button-primary button-large">选择文件</button>
                </div>
            </div>
        </div>
        
        <div class="upload-queue" id="upload-queue" style="display: none;">
            <h3><i class="dashicons dashicons-list-view"></i> 上传队列</h3>
            <div class="queue-controls">
                <button type="button" id="start-upload" class="button button-primary">开始上传</button>
                <button type="button" id="pause-upload" class="button">暂停上传</button>
                <button type="button" id="clear-queue" class="button">清空队列</button>
            </div>
            <div class="queue-list" id="queue-list"></div>
            
            <div class="upload-summary">
                <div class="summary-item">
                    <span class="label">总文件数：</span>
                    <span id="total-files">0</span>
                </div>
                <div class="summary-item">
                    <span class="label">已完成：</span>
                    <span id="completed-files">0</span>
                </div>
                <div class="summary-item">
                    <span class="label">上传中：</span>
                    <span id="uploading-files">0</span>
                </div>
                <div class="summary-item">
                    <span class="label">失败：</span>
                    <span id="failed-files">0</span>
                </div>
            </div>
            
            <div class="overall-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="overall-progress"></div>
                </div>
                <span class="progress-text" id="overall-progress-text">0%</span>
            </div>
        </div>
        
        <div class="upload-tips">
            <h3><i class="dashicons dashicons-info"></i> 使用说明</h3>
            <ul>
                <li><strong>批量上传：</strong>可同时选择多个视频文件进行上传</li>
                <li><strong>断点续传：</strong>网络中断时会自动重试，已上传的部分不会丢失</li>
                <li><strong>队列管理：</strong>可以暂停、继续或清空上传队列</li>
                <li><strong>自动分类：</strong>上传前可选择分类，便于后续管理</li>
                <li><strong>状态跟踪：</strong>实时显示每个文件的上传进度和状态</li>
            </ul>
        </div>
    </div>
</div>