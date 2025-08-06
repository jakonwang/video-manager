
<?php $__env->startSection('title', __('admin.system_settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in">
    <!-- 页面头部 -->
    <div class="flex items-center space-x-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-md">
            <i class="fas fa-cog text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                <?php echo e(__('admin.system_settings')); ?>

            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e(__('admin.manage_system_configuration')); ?></p>
        </div>
    </div>

    <!-- 设置表单 -->
    <div class="glass-effect rounded-xl shadow-md p-6">
        <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- 基本设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-globe mr-2 text-primary-600 text-sm"></i>
                    <?php echo e(__('admin.basic_settings')); ?>

                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 网站名称 -->
                    <div class="space-y-1.5">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-tag mr-1.5 text-primary-500 text-xs"></i>
                            <?php echo e(__('admin.site_name')); ?>

                        </label>
                        <input type="text" name="site_name" id="site_name" 
                            value="<?php echo e(old('site_name', $settings['site_name'] ?? '')); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- 管理员邮箱 -->
                    <div class="space-y-1.5">
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-envelope mr-1.5 text-primary-500 text-xs"></i>
                            <?php echo e(__('admin.admin_email')); ?>

                        </label>
                        <input type="email" name="admin_email" id="admin_email" 
                            value="<?php echo e(old('admin_email', $settings['admin_email'] ?? '')); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>
            </div>

            <!-- 文件设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-file-upload mr-2 text-accent-600 text-sm"></i>
                    <?php echo e(__('admin.file_settings')); ?>

                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 最大文件大小 -->
                    <div class="space-y-1.5">
                        <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-weight-hanging mr-1.5 text-accent-500 text-xs"></i>
                            <?php echo e(__('admin.max_file_size')); ?> (MB)
                        </label>
                        <input type="number" name="max_file_size" id="max_file_size" 
                            value="<?php echo e(old('max_file_size', $settings['max_file_size'] ?? 100)); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- 允许的文件类型 -->
                    <div class="space-y-1.5">
                        <label for="allowed_file_types" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-file-video mr-1.5 text-accent-500 text-xs"></i>
                            <?php echo e(__('admin.allowed_file_types')); ?>

                        </label>
                        <input type="text" name="allowed_file_types" id="allowed_file_types" 
                            value="<?php echo e(old('allowed_file_types', $settings['allowed_file_types'] ?? 'mp4,mov,avi')); ?>"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all duration-200">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="fas fa-info-circle mr-1.5 text-xs"></i>
                            <?php echo e(__('admin.file_types_help')); ?>

                        </p>
                    </div>
                </div>
            </div>

            <!-- 系统设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cogs mr-2 text-indigo-600 text-sm"></i>
                    <?php echo e(__('admin.system_configuration')); ?>

                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 系统语言 -->
                    <div class="space-y-1.5">
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-language mr-1.5 text-indigo-500 text-xs"></i>
                            <?php echo e(__('admin.system_language')); ?>

                        </label>
                        <select name="language" id="language"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <option value="zh" <?php echo e((old('language', $settings['language'] ?? 'zh') == 'zh') ? 'selected' : ''); ?>><?php echo e(__('admin.chinese')); ?></option>
                            <option value="en" <?php echo e((old('language', $settings['language'] ?? 'zh') == 'en') ? 'selected' : ''); ?>><?php echo e(__('admin.english')); ?></option>
                            <option value="vi" <?php echo e((old('language', $settings['language'] ?? 'zh') == 'vi') ? 'selected' : ''); ?>><?php echo e(__('admin.vietnamese')); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 腾讯云 COS 存储设置 -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cloud mr-2 text-blue-600 text-sm"></i>
                    腾讯云 COS 存储配置
                </h3>
                
                <!-- 启用 COS 开关 -->
                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="use_cos" id="use_cos" value="1" 
                        <?php echo e((old('use_cos', $settings['use_cos'] ?? false)) ? 'checked' : ''); ?>

                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="use_cos" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        启用腾讯云 COS 存储
                    </label>
                </div>
                
                <div id="cos-settings" class="space-y-4 <?php echo e((old('use_cos', $settings['use_cos'] ?? false)) ? '' : 'hidden'); ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Secret ID -->
                        <div class="space-y-1.5">
                            <label for="cos_secret_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-key mr-1.5 text-blue-500 text-xs"></i>
                                Secret ID
                            </label>
                            <input type="text" name="cos_secret_id" id="cos_secret_id" 
                                value="<?php echo e(old('cos_secret_id', $settings['cos_secret_id'] ?? '')); ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Secret Key -->
                        <div class="space-y-1.5">
                            <label for="cos_secret_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-lock mr-1.5 text-blue-500 text-xs"></i>
                                Secret Key
                            </label>
                            <input type="password" name="cos_secret_key" id="cos_secret_key" 
                                value="<?php echo e(old('cos_secret_key', $settings['cos_secret_key'] ?? '')); ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- 地域 -->
                        <div class="space-y-1.5">
                            <label for="cos_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-map-marker-alt mr-1.5 text-blue-500 text-xs"></i>
                                存储桶地域
                            </label>
                            <select name="cos_region" id="cos_region"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="ap-beijing" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-beijing') ? 'selected' : ''); ?>>北京 (ap-beijing)</option>
                                <option value="ap-shanghai" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-shanghai') ? 'selected' : ''); ?>>上海 (ap-shanghai)</option>
                                <option value="ap-guangzhou" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-guangzhou') ? 'selected' : ''); ?>>广州 (ap-guangzhou)</option>
                                <option value="ap-singapore" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-singapore') ? 'selected' : ''); ?>>新加坡 (ap-singapore)</option>
                                <option value="ap-hongkong" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-hongkong') ? 'selected' : ''); ?>>香港 (ap-hongkong)</option>
                                <option value="ap-tokyo" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-tokyo') ? 'selected' : ''); ?>>东京 (ap-tokyo)</option>
                                <option value="ap-seoul" <?php echo e((old('cos_region', $settings['cos_region'] ?? 'ap-beijing') == 'ap-seoul') ? 'selected' : ''); ?>>首尔 (ap-seoul)</option>
                            </select>
                        </div>

                        <!-- 存储桶名称 -->
                        <div class="space-y-1.5">
                            <label for="cos_bucket" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-database mr-1.5 text-blue-500 text-xs"></i>
                                存储桶名称
                            </label>
                            <input type="text" name="cos_bucket" id="cos_bucket" 
                                value="<?php echo e(old('cos_bucket', $settings['cos_bucket'] ?? '')); ?>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- 自定义域名 -->
                        <div class="space-y-1.5">
                            <label for="cos_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-link mr-1.5 text-blue-500 text-xs"></i>
                                自定义域名 (可选)
                            </label>
                            <input type="url" name="cos_domain" id="cos_domain" 
                                value="<?php echo e(old('cos_domain', $settings['cos_domain'] ?? '')); ?>"
                                placeholder="https://your-domain.com"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- 超时时间 -->
                        <div class="space-y-1.5">
                            <label for="cos_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-clock mr-1.5 text-blue-500 text-xs"></i>
                                请求超时时间 (秒)
                            </label>
                            <input type="number" name="cos_timeout" id="cos_timeout" 
                                value="<?php echo e(old('cos_timeout', $settings['cos_timeout'] ?? 60)); ?>"
                                min="10" max="600"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <!-- 测试连接按钮 -->
                    <div class="flex items-center space-x-3">
                        <button type="button" id="test-cos-connection" 
                            class="inline-flex items-center space-x-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plug text-sm"></i>
                            <span>测试连接</span>
                        </button>
                        <div id="cos-test-result" class="text-sm"></div>
                    </div>
                </div>
            </div>

            <!-- 操作按钮 -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="reset" class="inline-flex items-center space-x-2 rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-undo text-sm"></i>
                    <span><?php echo e(__('admin.reset')); ?></span>
                </button>
                <button type="submit" class="inline-flex items-center space-x-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-save text-sm"></i>
                    <span><?php echo e(__('admin.save')); ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const useCosCheckbox = document.getElementById('use_cos');
    const cosSettings = document.getElementById('cos-settings');
    const testButton = document.getElementById('test-cos-connection');
    const testResult = document.getElementById('cos-test-result');

    // 切换 COS 设置显示/隐藏
    useCosCheckbox.addEventListener('change', function() {
        if (this.checked) {
            cosSettings.classList.remove('hidden');
        } else {
            cosSettings.classList.add('hidden');
        }
    });

    // 测试 COS 连接
    testButton.addEventListener('click', function() {
        const secretId = document.getElementById('cos_secret_id').value;
        const secretKey = document.getElementById('cos_secret_key').value;
        const region = document.getElementById('cos_region').value;
        const bucket = document.getElementById('cos_bucket').value;

        if (!secretId || !secretKey || !region || !bucket) {
            testResult.innerHTML = '<span class="text-red-600">请填写完整的 COS 配置信息</span>';
            return;
        }

        // 显示加载状态
        testButton.disabled = true;
        testButton.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i><span>测试中...</span>';
        testResult.innerHTML = '';

        // 发送测试请求
        fetch('<?php echo e(route("admin.settings.test-cos")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                cos_secret_id: secretId,
                cos_secret_key: secretKey,
                cos_region: region,
                cos_bucket: bucket
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                testResult.innerHTML = '<span class="text-green-600">✅ ' + data.message + '</span>';
            } else {
                testResult.innerHTML = '<span class="text-red-600">❌ ' + data.message + '</span>';
            }
        })
        .catch(error => {
            testResult.innerHTML = '<span class="text-red-600">❌ 测试失败：' + error.message + '</span>';
        })
        .finally(() => {
            // 恢复按钮状态
            testButton.disabled = false;
            testButton.innerHTML = '<i class="fas fa-plug text-sm"></i><span>测试连接</span>';
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\phpstudy_pro\WWW\video-manager\resources\views/admin/settings.blade.php ENDPATH**/ ?>