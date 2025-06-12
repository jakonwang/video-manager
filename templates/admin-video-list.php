<?php
// templates/admin-video-list.php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap video-manager-wrap">
    <h1><i class="dashicons dashicons-format-video"></i> 视频列表管理</h1>
    
    <div class="video-manager-container">
        <div class="list-controls">
            <div class="search-box">
                <input type="text" id="search-videos" placeholder="搜索视频标题..." class="regular-text">
                <button type="button" id="search-btn" class="button">搜索</button>
            </div>
            
            <div class="filter-box">
                <select id="filter-status">
                    <option value="">所有状态</option>
                    <option value="unused">未使用</option>
                    <option value="used">已使用</option>
                </select>
                
                <select id="filter-category">
                    <option value="">所有分类</option>
                    <option value="0">无分类</option>
                </select>
                
                <button type="button" id="filter-btn" class="button">筛选</button>
                <button type="button" id="reset-filter" class="button">重置</button>
            </div>
            
            <div class="bulk-actions">
                <select id="bulk-action">
                    <option value="">批量操作</option>
                    <option value="mark-used">标记为已使用</option>
                    <option value="mark-unused">标记为未使用</option>
                    <option value="delete">删除</option>
                </select>
                <button type="button" id="apply-bulk" class="button">应用</button>
            </div>
        </div>
        
        <div class="videos-table-wrapper">
            <div class="videos-table-container">
                <table class="wp-list-table widefat fixed striped videos-table">
                    <thead>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" id="select-all-videos">
                            </th>
                            <th class="column-thumbnail">缩略图</th>
                            <th class="column-title">标题</th>
                            <th class="column-filename">文件名</th>
                            <th class="column-size">文件大小</th>
                            <th class="column-category">分类</th>
                            <th class="column-status">状态</th>
                            <th class="column-upload-time">上传时间</th>
                            <th class="column-actions">操作</th>
                        </tr>
                    </thead>
                    <tbody id="videos-table-body">
                        <tr>
                            <td colspan="9" class="loading-row">
                                <div class="loading-spinner"></div>
                                <span>加载中...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="tablenav bottom">
                <div class="alignleft actions">
                    <div class="tablenav-pages">
                        <span class="displaying-num" id="displaying-num">共 0 项</span>
                        <span class="pagination-links">
                            <button class="button" id="first-page" disabled>«</button>
                            <button class="button" id="prev-page" disabled>‹</button>
                            <span class="screen-reader-text">当前页</span>
                            <span id="current-page-selector" class="current-page">
                                <input type="number" id="current-page" value="1" min="1" class="current-page">
                                <span class="tablenav-paging-text"> / <span id="total-pages">1</span></span>
                            </span>
                            <button class="button" id="next-page" disabled>›</button>
                            <button class="button" id="last-page" disabled>»</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 视频预览模态框 -->
<div id="video-preview-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">视频预览</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <video id="preview-video" controls width="100%" style="max-height: 400px;">
                您的浏览器不支持视频播放。
            </video>
            <div class="video-info">
                <p><strong>文件名：</strong><span id="modal-filename"></span></p>
                <p><strong>文件大小：</strong><span id="modal-filesize"></span></p>
                <p><strong>上传时间：</strong><span id="modal-upload-time"></span></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="button button-primary" id="download-video">下载视频</button>
            <button type="button" class="button" id="close-modal">关闭</button>
        </div>
    </div>
</div>