<?php
// templates/admin-categories-i18n.php - 增强的分类管理页面
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap video-manager-wrap">
    <h1><i class="dashicons dashicons-category"></i> <?php _e('Categories', 'video-manager'); ?></h1>
    
    <div class="video-manager-container">
        <div class="categories-layout">
            <div class="add-category-section">
                <div class="category-form-card">
                    <h2><i class="dashicons dashicons-plus-alt"></i> <?php _e('Add New Category', 'video-manager'); ?></h2>
                    
                    <form id="add-category-form">
                        <div class="form-field">
                            <label for="category-name"><?php _e('Category Name', 'video-manager'); ?> <span class="required">*</span></label>
                            <input type="text" id="category-name" name="name" required class="regular-text" placeholder="<?php _e('Enter category name...', 'video-manager'); ?>">
                            <p class="description"><?php _e('Display name for the category', 'video-manager'); ?></p>
                        </div>
                        
                        <div class="form-field">
                            <label for="category-name-en"><?php _e('Category Name (English)', 'video-manager'); ?></label>
                            <input type="text" id="category-name-en" name="name_en" class="regular-text" placeholder="<?php _e('Enter English category name...', 'video-manager'); ?>">
                            <p class="description"><?php _e('English display name for the category (optional)', 'video-manager'); ?></p>
                        </div>
                        
                        <div class="form-field">
                            <label for="category-slug"><?php _e('Category Slug', 'video-manager'); ?></label>
                            <input type="text" id="category-slug" name="slug" class="regular-text" placeholder="<?php _e('Auto-generated from name', 'video-manager'); ?>">
                            <p class="description"><?php _e('URL slug for the category, auto-generated if empty', 'video-manager'); ?></p>
                        </div>
                        
                        <div class="form-field">
                            <label for="category-description"><?php _e('Category Description', 'video-manager'); ?></label>
                            <textarea id="category-description" name="description" rows="4" class="regular-text" placeholder="<?php _e('Enter category description...', 'video-manager'); ?>"></textarea>
                            <p class="description"><?php _e('Optional category description', 'video-manager'); ?></p>
                        </div>
                        
                        <div class="form-field">
                            <label for="category-description-en"><?php _e('Category Description (English)', 'video-manager'); ?></label>
                            <textarea id="category-description-en" name="description_en" rows="4" class="regular-text" placeholder="<?php _e('Enter English category description...', 'video-manager'); ?>"></textarea>
                            <p class="description"><?php _e('Optional English category description', 'video-manager'); ?></p>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="button button-primary">
                                <i class="dashicons dashicons-yes"></i> <?php _e('Add Category', 'video-manager'); ?>
                            </button>
                            <button type="reset" class="button">
                                <i class="dashicons dashicons-undo"></i> <?php _e('Reset', 'video-manager'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="categories-list-section">
                <div class="categories-header">
                    <h2><i class="dashicons dashicons-list-view"></i> <?php _e('Category List', 'video-manager'); ?></h2>
                    <button type="button" id="refresh-categories" class="button">
                        <i class="dashicons dashicons-update"></i> <?php _e('Refresh', 'video-manager'); ?>
                    </button>
                </div>
                
                <div class="categories-grid" id="categories-grid">
                    <div class="loading-category">
                        <div class="loading-spinner"></div>
                        <span><?php _e('Loading categories...', 'video-manager'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="categories-help">
            <h3><i class="dashicons dashicons-info"></i> <?php _e('Usage Instructions', 'video-manager'); ?></h3>
            <div class="help-content">
                <div class="help-item">
                    <h4><?php _e('Create Category', 'video-manager'); ?></h4>
                    <p><?php _e('Fill in the category name and optional description, the system will automatically generate URL slug', 'video-manager'); ?></p>
                </div>
                <div class="help-item">
                    <h4><?php _e('Category Links', 'video-manager'); ?></h4>
                    <p><?php _e('Each category has an independent frontend display link that can be copied and shared', 'video-manager'); ?></p>
                </div>
                <div class="help-item">
                    <h4><?php _e('Video Management', 'video-manager'); ?></h4>
                    <p><?php _e('You can select categories when uploading videos, or modify categories in the video list', 'video-manager'); ?></p>
                </div>
                <div class="help-item">
                    <h4><?php _e('Multilingual Content', 'video-manager'); ?></h4>
                    <p><?php _e('When creating categories, you can provide content in both languages for better user experience', 'video-manager'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 编辑分类模态框 -->
<div id="edit-category-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="dashicons dashicons-edit"></i> <?php _e('Edit Category', 'video-manager'); ?></h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="edit-category-form">
                <input type="hidden" id="edit-category-id">
                
                <div class="form-field">
                    <label for="edit-category-name"><?php _e('Category Name', 'video-manager'); ?> <span class="required">*</span></label>
                    <input type="text" id="edit-category-name" name="name" required class="regular-text">
                </div>
                
                <div class="form-field">
                    <label for="edit-category-name-en"><?php _e('Category Name (English)', 'video-manager'); ?></label>
                    <input type="text" id="edit-category-name-en" name="name_en" class="regular-text">
                </div>
                
                <div class="form-field">
                    <label for="edit-category-slug"><?php _e('Category Slug', 'video-manager'); ?></label>
                    <input type="text" id="edit-category-slug" name="slug" class="regular-text">
                </div>
                
                <div class="form-field">
                    <label for="edit-category-description"><?php _e('Category Description', 'video-manager'); ?></label>
                    <textarea id="edit-category-description" name="description" rows="4" class="regular-text"></textarea>
                </div>
                
                <div class="form-field">
                    <label for="edit-category-description-en"><?php _e('Category Description (English)', 'video-manager'); ?></label>
                    <textarea id="edit-category-description-en" name="description_en" rows="4" class="regular-text"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="button button-primary" id="save-category">
                <i class="dashicons dashicons-yes"></i> <?php _e('Save Changes', 'video-manager'); ?>
            </button>
            <button type="button" class="button" id="cancel-edit"><?php _e('Cancel', 'video-manager'); ?></button>
        </div>
    </div>
</div>

<!-- 删除确认模态框 -->
<div id="delete-category-modal" class="modal" style="display: none;">
    <div class="modal-content small">
        <div class="modal-header">
            <h3><i class="dashicons dashicons-warning"></i> <?php _e('Confirm Delete', 'video-manager'); ?></h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p><?php _e('Are you sure you want to delete category', 'video-manager'); ?> <strong id="delete-category-name"></strong>?</p>
            <p class="description"><?php _e('Deleting this category will move all videos in it to "No Category" status. This action cannot be undone.', 'video-manager'); ?></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="button button-primary button-delete" id="confirm-delete">
                <i class="dashicons dashicons-trash"></i> <?php _e('Confirm Delete', 'video-manager'); ?>
            </button>
            <button type="button" class="button" id="cancel-delete"><?php _e('Cancel', 'video-manager'); ?></button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 更新表单验证的错误消息
    const i18n = {
        categoryNameRequired: '<?php _e("Please enter category name", "video-manager"); ?>',
        categoryCreated: '<?php _e("Category created successfully", "video-manager"); ?>',
        categoryCreateFailed: '<?php _e("Category creation failed, slug may already exist", "video-manager"); ?>',
        categoryUpdated: '<?php _e("Category updated successfully", "video-manager"); ?>',
        categoryUpdateFailed: '<?php _e("Category update failed", "video-manager"); ?>',
        categoryDeleted: '<?php _e("Category deleted successfully", "video-manager"); ?>',
        categoryDeleteFailed: '<?php _e("Category deletion failed", "video-manager"); ?>',
        confirmDelete: '<?php _e("Are you sure you want to delete this category?", "video-manager"); ?>',
        networkError: '<?php _e("Network error, please try again", "video-manager"); ?>'
    };
    
    // 创建类别表单提交
    $('#add-category-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'create_category',
            nonce: videoManager.nonce,
            name: $('#category-name').val().trim(),
            name_en: $('#category-name-en').val().trim(),
            slug: $('#category-slug').val().trim(),
            description: $('#category-description').val().trim(),
            description_en: $('#category-description-en').val().trim()
        };
        
        if (!formData.name) {
            showNotice(i18n.categoryNameRequired, 'warning');
            return;
        }
        
        $.ajax({
            url: videoManager.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showNotice(i18n.categoryCreated, 'success');
                    $('#add-category-form')[0].reset();
                    loadCategoriesGrid();
                    loadCategories(); // 刷新其他页面的分类选择器
                } else {
                    showNotice(i18n.categoryCreateFailed, 'error');
                }
            },
            error: function() {
                showNotice(i18n.networkError, 'error');
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
    
    // 其他JavaScript功能保持不变，但添加国际化支持...
});
</script>