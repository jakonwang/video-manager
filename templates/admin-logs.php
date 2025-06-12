<?php
// templates/admin-logs.php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap video-manager-wrap">
    <h1><i class="dashicons dashicons-list-view"></i> 上传日志</h1>
    
    <div class="video-manager-container">
        <div class="logs-controls">
            <div class="logs-filter">
                <select id="filter-log-status">
                    <option value="">所有状态</option>
                    <option value="success">成功</option>
                    <option value="error">错误</option>
                </select>
                
                <select id="filter-log-action">
                    <option value="">所有操作</option>
                    <option value="upload">上传</option>
                    <option value="delete">删除</option>
                    <option value="update">更新</option>
                </select>
                
                <input type="date" id="filter-date-start" class="regular-text">
                <span>至</span>
                <input type="date" id="filter-date-end" class="regular-text">
                
                <button type="button" id="filter-logs" class="button">筛选</button>
                <button type="button" id="reset-log-filter" class="button">重置</button>
            </div>
            
            <div class="logs-actions">
                <button type="button" id="export-logs" class="button">
                    <i class="dashicons dashicons-download"></i> 导出日志
                </button>
                <button type="button" id="clear-logs" class="button button-delete">
                    <i class="dashicons dashicons-trash"></i> 清空日志
                </button>
            </div>
        </div>
        
        <div class="logs-stats">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="dashicons dashicons-yes-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="success-count">0</div>
                    <div class="stat-label">成功操作</div>
                </div>
            </div>
            
            <div class="stat-card error">
                <div class="stat-icon">
                    <i class="dashicons dashicons-dismiss"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="error-count">0</div>
                    <div class="stat-label">错误操作</div>
                </div>
            </div>
            
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="dashicons dashicons-chart-bar"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="total-logs">0</div>
                    <div class="stat-label">总日志数</div>
                </div>
            </div>
            
            <div class="stat-card today">
                <div class="stat-icon">
                    <i class="dashicons dashicons-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="today-logs">0</div>
                    <div class="stat-label">今日日志</div>
                </div>
            </div>
        </div>
        
        <div class="logs-table-container">
            <table class="wp-list-table widefat fixed striped logs-table">
                <thead>
                    <tr>
                        <th class="column-status">状态</th>
                        <th class="column-action">操作类型</th>
                        <th class="column-message">消息内容</th>
                        <th class="column-time">时间</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body">
                    <tr>
                        <td colspan="4" class="loading-row">
                            <div class="loading-spinner"></div>
                            <span>加载日志中...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="tablenav bottom">
            <div class="alignleft actions">
                <div class="tablenav-pages">
                    <span class="displaying-num" id="logs-displaying-num">共 0 项</span>
                    <span class="pagination-links">
                        <button class="button" id="logs-first-page" disabled>«</button>
                        <button class="button" id="logs-prev-page" disabled>‹</button>
                        <span class="screen-reader-text">当前页</span>
                        <span id="logs-current-page-selector" class="current-page">
                            <input type="number" id="logs-current-page" value="1" min="1" class="current-page">
                            <span class="tablenav-paging-text"> / <span id="logs-total-pages">1</span></span>
                        </span>
                        <button class="button" id="logs-next-page" disabled>›</button>
                        <button class="button" id="logs-last-page" disabled>»</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 日志详情模态框 -->
<div id="log-detail-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="dashicons dashicons-info"></i> 日志详情</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="log-detail">
                <div class="detail-row">
                    <label>状态：</label>
                    <span id="detail-status" class="status-badge"></span>
                </div>
                <div class="detail-row">
                    <label>操作类型：</label>
                    <span id="detail-action"></span>
                </div>
                <div class="detail-row">
                    <label>时间：</label>
                    <span id="detail-time"></span>
                </div>
                <div class="detail-row full-width">
                    <label>详细消息：</label>
                    <div id="detail-message" class="message-content"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="button" id="close-log-detail">关闭</button>
        </div>
    </div>
</div>