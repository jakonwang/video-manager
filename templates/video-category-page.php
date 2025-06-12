<?php
// templates/video-category-page.php - 独立的前端页面模板
if (!defined('ABSPATH')) {
    exit;
}

global $video_category_data;
$category = $video_category_data;

// 确保有分类数据
if (!$category) {
    wp_die('分类数据不存在');
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html($category->name); ?>  | <?php bloginfo('name'); ?></title>
    
    <!-- 加载WordPress头部 -->
    <?php wp_head(); ?>
    
    <!-- 基础样式 -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            min-height: calc(100vh - 40px);
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .page-header > * {
            position: relative;
            z-index: 1;
        }
        
        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #fff, #f0f8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .page-description {
            font-size: 1.2rem;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .page-content {
            padding: 40px;
        }
        
        /* 调试信息样式 */
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .page-container {
                border-radius: 8px;
                min-height: calc(100vh - 20px);
            }
            
            .page-header {
                padding: 40px 20px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .page-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title"><?php echo esc_html($category->name); ?></h1>
            <?php if ($category->description): ?>
                <p class="page-description"><?php echo esc_html($category->description); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="page-content">
            <!-- 调试信息 -->
            
            
            <?php echo do_shortcode('[video_category_display category="' . $category->slug . '"]'); ?>
        </div>
    </div>
    
    <!-- 手动输出JavaScript配置 -->
    <script type="text/javascript">
        var videoManagerFrontend = {
            ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('video_manager_frontend_nonce'); ?>'
        };
        console.log('手动设置 videoManagerFrontend:', videoManagerFrontend);
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>