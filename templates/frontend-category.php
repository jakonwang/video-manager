<?php
// templates/frontend-category.php - 精简版本
if (!defined('ABSPATH')) {
    exit;
}

$category_slug = $atts['category'];
?>

<div class="video-category-display" data-category="<?php echo esc_attr($category_slug); ?>">
    <!-- 状态提示 -->
    <div class="status-wrapper">
        <p id="video-status-text" class="status-text">Assigning video for you...</p>
    </div>
    
    <!-- 视频显示容器 -->
    <div id="single-video-container" class="video-container" style="display: none;">
        <div class="video-card" id="video-card-single">
            <!-- 视频内容将通过JavaScript动态插入 -->
        </div>
        
        <div class="video-actions">
            <button type="button" id="preview-video-btn" class="btn btn-preview">
                👁️ Preview
            </button>
            <button type="button" id="download-video-btn" class="btn btn-download">
                ⬇️ Download
            </button>
        </div>
    </div>
    
    <!-- 无视频消息 -->
    <div id="no-video-message" class="no-video-message" style="display: none;">
        <div class="empty-state">
            <div class="empty-icon">📹</div>
            <h3>No Video Available</h3>
            <p id="no-video-reason">Checking available videos...</p>
        </div>
    </div>
</div>

<!-- 视频预览模态框 -->
<div id="video-modal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-video-title">Video Preview</h3>
            <button type="button" class="close-btn" id="close-modal">&times;</button>
        </div>
        
        <div class="modal-body">
            <div class="video-player">
                <video id="modal-video" controls preload="metadata" style="width: 100%; height: auto;">
                    <source id="modal-video-source" src="" type="video/mp4">
                    Your browser does not support video playback.
                </video>
            </div>
            
            <div class="video-details">
                <div class="detail-item">
                    <span class="label">📄 File:</span>
                    <span id="modal-filename" class="value"></span>
                </div>
                <div class="detail-item">
                    <span class="label">📏 Size:</span>
                    <span id="modal-filesize" class="value"></span>
                </div>
                <div class="detail-item">
                    <span class="label">📅 Date:</span>
                    <span id="modal-upload-time" class="value"></span>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-download" id="modal-download">
                ⬇️ Download
            </button>
            <button type="button" class="btn btn-gray" id="modal-close">
                Close
            </button>
        </div>
    </div>
</div>

<style>
/* 精简版前端样式 */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.video-category-display {
    max-width: 500px;
    margin: 0 auto;
    padding: 15px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: transparent;
    color: #2d3748;
    line-height: 1.5;
}

/* 状态提示 - 精简 */
.status-wrapper {
    text-align: center;
    margin-bottom: 15px;
}

.status-text {
    font-size: 14px;
    color: #4a5568;
    padding: 8px 16px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

/* 视频容器 - 精简 */
.video-container {
    margin-bottom: 15px;
}

.video-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

/* 精简的视频信息显示 */
.video-compact {
    text-align: center;
}

.video-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 8px;
    line-height: 1.3;
}

.video-meta {
    display: flex;
    justify-content: center;
    gap: 12px;
    font-size: 12px;
    color: #718096;
    flex-wrap: wrap;
}

.video-meta span {
    background: #f7fafc;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}

/* 视频操作按钮 - 精简 */
.video-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* 按钮样式 - 精简 */
.btn {
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 110px;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.btn-preview {
    background: #4299e1;
    color: white;
}

.btn-preview:hover {
    background: #3182ce;
}

.btn-download {
    background: #48bb78;
    color: white;
}

.btn-download:hover {
    background: #38a169;
}

.btn-gray {
    background: #a0aec0;
    color: white;
}

.btn-gray:hover {
    background: #718096;
}

/* 精简按钮样式 */
.btn-compact {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 5px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-download-compact {
    background: #48bb78;
    color: white;
}

.btn-download-compact:hover {
    background: #38a169;
}

.btn-cancel-compact {
    background: #a0aec0;
    color: white;
}

.btn-cancel-compact:hover {
    background: #718096;
}

.btn-refresh-compact {
    background: #4299e1;
    color: white;
}

.btn-refresh-compact:hover {
    background: #3182ce;
}

/* 空状态 - 精简 */
.no-video-message {
    text-align: center;
}

.empty-state {
    background: white;
    border-radius: 8px;
    padding: 30px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.empty-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 0.9rem;
    color: #718096;
}

/* 模态框 - 精简 */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.show {
    display: flex !important;
    opacity: 1 !important;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    background: white;
    border-radius: 8px;
    max-width: 90vw;
    max-height: 90vh;
    width: 100%;
    max-width: 600px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 15px 20px;
    background: #f7fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #718096;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.close-btn:hover {
    background: #e2e8f0;
    color: #2d3748;
}

.modal-body {
    padding: 20px;
}

.video-player {
    margin-bottom: 15px;
    border-radius: 6px;
    overflow: hidden;
}

.video-details {
    background: #f7fafc;
    border-radius: 6px;
    padding: 15px;
    border: 1px solid #e2e8f0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item .label {
    font-weight: 500;
    color: #4a5568;
}

.detail-item .value {
    color: #2d3748;
    word-break: break-all;
}

.modal-footer {
    padding: 15px 20px;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* 精简的下载确认模态框 */
.download-confirm-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1001;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.download-confirm-modal.show {
    display: flex !important;
    opacity: 1 !important;
}

.modal-content-compact {
    position: relative;
    background: white;
    border-radius: 8px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header-compact {
    padding: 15px 20px;
    background: #fed7d7;
    color: #c53030;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px 8px 0 0;
}

.modal-header-compact h3 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.modal-body-compact {
    padding: 20px;
    text-align: center;
}

.modal-body-compact p {
    margin-bottom: 12px;
    font-size: 14px;
    line-height: 1.5;
}

.warning-compact {
    color: #718096;
    font-size: 13px !important;
}

.modal-footer-compact {
    padding: 15px 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
}

/* 加载动画 - 精简 */
.loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #e2e8f0;
    border-top: 2px solid #4299e1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 6px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 响应式设计 - 精简 */
@media (max-width: 480px) {
    .video-category-display {
        padding: 10px;
    }
    
    .video-card {
        padding: 12px;
    }
    
    .video-title {
        font-size: 1rem;
    }
    
    .video-meta {
        font-size: 11px;
        gap: 8px;
    }
    
    .video-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        min-width: auto;
    }
    
    .modal-content {
        max-width: 95vw;
        margin: 10px;
    }
    
    .modal-content-compact {
        max-width: 95vw;
        margin: 10px;
    }
    
    .modal-body {
        padding: 15px;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal-footer-compact {
        flex-direction: column;
    }
}
</style>