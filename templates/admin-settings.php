<?php
// templates/admin-settings.php
if (!defined('ABSPATH')) {
    exit;
}

$current_language = get_option('video_manager_language', 'zh_CN');
?>

<div class="wrap video-manager-wrap">
    <h1><i class="dashicons dashicons-admin-settings"></i> <?php _e('Settings', 'video-manager'); ?></h1>
    
    <div class="video-manager-container">
        <div class="settings-layout">
            <div class="settings-section">
                <div class="settings-card">
                    <h2><i class="dashicons dashicons-translation"></i> <?php _e('Language Settings', 'video-manager'); ?></h2>
                    <p class="description"><?php _e('Choose your preferred language for the admin interface and frontend display.', 'video-manager'); ?></p>
                    
                    <form id="language-settings-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="video_manager_language"><?php _e('Interface Language', 'video-manager'); ?></label>
                                </th>
                                <td>
                                    <select id="video_manager_language" name="video_manager_language" class="regular-text">
                                        <option value="zh_CN" <?php selected($current_language, 'zh_CN'); ?>>
                                            中文 (简体) / Chinese (Simplified)
                                        </option>
                                        <option value="en_US" <?php selected($current_language, 'en_US'); ?>>
                                            English (US)
                                        </option>
                                    </select>
                                    <p class="description">
                                        <?php _e('Select the language for admin interface, frontend display, and email notifications.', 'video-manager'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="form-actions">
                            <button type="submit" class="button button-primary" id="save-language-settings">
                                <i class="dashicons dashicons-yes"></i> <?php _e('Save Changes', 'video-manager'); ?>
                            </button>
                            <button type="button" class="button" id="reset-settings">
                                <i class="dashicons dashicons-undo"></i> <?php _e('Reset to Default', 'video-manager'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="settings-section">
                <div class="settings-card">
                    <h2><i class="dashicons dashicons-admin-tools"></i> <?php _e('System Information', 'video-manager'); ?></h2>
                    
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Setting', 'video-manager'); ?></th>
                                <th><?php _e('Value', 'video-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php _e('Plugin Version', 'video-manager'); ?></td>
                                <td><?php echo VIDEO_MANAGER_VERSION; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('WordPress Version', 'video-manager'); ?></td>
                                <td><?php echo get_bloginfo('version'); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('PHP Version', 'video-manager'); ?></td>
                                <td><?php echo PHP_VERSION; ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('MySQL Version', 'video-manager'); ?></td>
                                <td><?php echo $GLOBALS['wpdb']->db_version(); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Max Upload Size', 'video-manager'); ?></td>
                                <td><?php echo size_format(wp_max_upload_size()); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Max Execution Time', 'video-manager'); ?></td>
                                <td><?php echo ini_get('max_execution_time'); ?> <?php _e('seconds', 'video-manager'); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Memory Limit', 'video-manager'); ?></td>
                                <td><?php echo ini_get('memory_limit'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="settings-section">
                <div class="settings-card">
                    <h2><i class="dashicons dashicons-chart-pie"></i> <?php _e('Statistics', 'video-manager'); ?></h2>
                    
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number" id="total-videos-count">0</div>
                            <div class="stat-label"><?php _e('Total Videos', 'video-manager'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" id="total-categories-count">0</div>
                            <div class="stat-label"><?php _e('Total Categories', 'video-manager'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" id="total-downloads-count">0</div>
                            <div class="stat-label"><?php _e('Total Downloads', 'video-manager'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" id="storage-used">0</div>
                            <div class="stat-label"><?php _e('Storage Used', 'video-manager'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="settings-section">
                <div class="settings-card">
                    <h2><i class="dashicons dashicons-admin-tools"></i> <?php _e('Maintenance Tools', 'video-manager'); ?></h2>
                    <p class="description"><?php _e('Use these tools to maintain your video library.', 'video-manager'); ?></p>
                    
                    <div class="maintenance-tools">
                        <div class="tool-item">
                            <h4><?php _e('Clean Temporary Files', 'video-manager'); ?></h4>
                            <p><?php _e('Remove temporary upload files that may have been left behind.', 'video-manager'); ?></p>
                            <button type="button" class="button" id="clean-temp-files">
                                <i class="dashicons dashicons-trash"></i> <?php _e('Clean Now', 'video-manager'); ?>
                            </button>
                        </div>
                        
                        <div class="tool-item">
                            <h4><?php _e('Regenerate Video URLs', 'video-manager'); ?></h4>
                            <p><?php _e('Update video file paths if you have moved your WordPress installation.', 'video-manager'); ?></p>
                            <button type="button" class="button" id="regenerate-urls">
                                <i class="dashicons dashicons-update"></i> <?php _e('Regenerate', 'video-manager'); ?>
                            </button>
                        </div>
                        
                        <div class="tool-item">
                            <h4><?php _e('Export Settings', 'video-manager'); ?></h4>
                            <p><?php _e('Export plugin settings and data for backup purposes.', 'video-manager'); ?></p>
                            <button type="button" class="button" id="export-settings">
                                <i class="dashicons dashicons-download"></i> <?php _e('Export', 'video-manager'); ?>
                            </button>
                        </div>
                        
                        <div class="tool-item">
                            <h4><?php _e('Import Settings', 'video-manager'); ?></h4>
                            <p><?php _e('Import previously exported settings and data.', 'video-manager'); ?></p>
                            <input type="file" id="import-file" accept=".json" style="display: none;">
                            <button type="button" class="button" id="import-settings">
                                <i class="dashicons dashicons-upload"></i> <?php _e('Import', 'video-manager'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="settings-help">
            <h3><i class="dashicons dashicons-info"></i> <?php _e('Help & Support', 'video-manager'); ?></h3>
            <div class="help-content">
                <div class="help-item">
                    <h4><?php _e('Language Switching', 'video-manager'); ?></h4>
                    <p><?php _e('Changes take effect immediately after saving. Both admin interface and frontend will use the selected language.', 'video-manager'); ?></p>
                </div>
                <div class="help-item">
                    <h4><?php _e('Multilingual Content', 'video-manager'); ?></h4>
                    <p><?php _e('When creating categories and uploading videos, you can provide content in both languages for better user experience.', 'video-manager'); ?></p>
                </div>
                <div class="help-item">
                    <h4><?php _e('Backup Recommendations', 'video-manager'); ?></h4>
                    <p><?php _e('Regularly export your settings and backup your video files to prevent data loss.', 'video-manager'); ?></p>
                </div>
                <div class="help-item">
                    <h4><?php _e('Performance Tips', 'video-manager'); ?></h4>
                    <p><?php _e('Clean temporary files regularly and optimize your server settings for better video upload performance.', 'video-manager'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.settings-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.settings-card h2 {
    margin: 0 0 20px 0;
    color: #23282d;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-actions {
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border-left: 4px solid #0073aa;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #0073aa;
    line-height: 1;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: #646970;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.maintenance-tools {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.tool-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e2e4e7;
}

.tool-item h4 {
    margin: 0 0 10px 0;
    color: #23282d;
}

.tool-item p {
    margin: 0 0 15px 0;
    color: #646970;
    font-size: 13px;
    line-height: 1.5;
}

.settings-help {
    background: #f0f6ff;
    border: 1px solid #0073aa;
    border-radius: 8px;
    padding: 25px;
}

.settings-help h3 {
    margin: 0 0 20px 0;
    color: #0073aa;
    display: flex;
    align-items: center;
    gap: 8px;
}

.help-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.help-item h4 {
    margin: 0 0 8px 0;
    color: #23282d;
}

.help-item p {
    margin: 0;
    color: #646970;
    line-height: 1.5;
    font-size: 13px;
}

@media (max-width: 768px) {
    .stats-grid,
    .maintenance-tools,
    .help-content {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .settings-card {
        padding: 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // 保存语言设置
    $('#language-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        const language = $('#video_manager_language').val();
        
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'switch_language',
                nonce: videoManager.nonce,
                language: language
            },
            success: function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    // 刷新页面以应用新语言
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotice(response.data, 'error');
                }
            },
            error: function() {
                showNotice('<?php _e("Network error, please try again", "video-manager"); ?>', 'error');
            }
        });
    });
    
    // 重置设置
    $('#reset-settings').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to reset to default settings?", "video-manager"); ?>')) {
            $('#video_manager_language').val('zh_CN');
        }
    });
    
    // 加载统计数据
    loadStatistics();
    
    function loadStatistics() {
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_system_stats',
                nonce: videoManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    const stats = response.data;
                    $('#total-videos-count').text(stats.total_videos || 0);
                    $('#total-categories-count').text(stats.total_categories || 0);
                    $('#total-downloads-count').text(stats.total_downloads || 0);
                    $('#storage-used').text(stats.storage_used || '0 B');
                }
            }
        });
    }
    
    // 维护工具
    $('#clean-temp-files').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to clean temporary files?", "video-manager"); ?>')) {
            performMaintenance('clean_temp_files', $(this));
        }
    });
    
    $('#regenerate-urls').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to regenerate video URLs?", "video-manager"); ?>')) {
            performMaintenance('regenerate_urls', $(this));
        }
    });
    
    $('#export-settings').on('click', function() {
        performMaintenance('export_settings', $(this));
    });
    
    $('#import-settings').on('click', function() {
        $('#import-file').click();
    });
    
    $('#import-file').on('change', function() {
        const file = this.files[0];
        if (file && file.type === 'application/json') {
            const formData = new FormData();
            formData.append('action', 'import_settings');
            formData.append('nonce', videoManager.nonce);
            formData.append('file', file);
            
            $.ajax({
                url: videoManager.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotice(response.data.message, 'success');
                    } else {
                        showNotice(response.data, 'error');
                    }
                }
            });
        }
    });
    
    function performMaintenance(action, button) {
        const originalText = button.text();
        button.prop('disabled', true).text('<?php _e("Processing...", "video-manager"); ?>');
        
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: {
                action: action,
                nonce: videoManager.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    if (action === 'export_settings' && response.data.download_url) {
                        // 触发下载
                        const a = document.createElement('a');
                        a.href = response.data.download_url;
                        a.download = response.data.filename;
                        a.click();
                    }
                } else {
                    showNotice(response.data, 'error');
                }
            },
            error: function() {
                showNotice('<?php _e("Operation failed", "video-manager"); ?>', 'error');
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    }
    
    function showNotice(message, type) {
        const notice = $(`
            <div class="notice notice-${type} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text"><?php _e("Dismiss this notice", "video-manager"); ?></span>
                </button>
            </div>
        `);
        
        $('.wrap').prepend(notice);
        
        setTimeout(() => {
            notice.fadeOut(() => notice.remove());
        }, 5000);
        
        notice.find('.notice-dismiss').on('click', function() {
            notice.fadeOut(() => notice.remove());
        });
    }
});
</script>