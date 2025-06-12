<?php $__env->startSection('title', __('admin.video_list')); ?>

<?php $__env->startSection('content'); ?>
<div x-data="{ showPreview: false, videoUrl: '' }">
    <!-- 页面头部 -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-md">
                <i class="fas fa-film text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    <?php echo e(__('admin.video_list')); ?>

                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">管理和查看所有视频文件</p>
            </div>
        </div>
        <a href="<?php echo e(route('admin.videos.create')); ?>" class="inline-flex items-center space-x-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white shadow-md hover:from-primary-700 hover:to-primary-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200 hover:shadow-lg">
            <i class="fas fa-plus text-sm"></i>
            <span><?php echo e(__('admin.upload_video')); ?></span>
        </a>
    </div>

    <!-- 筛选和搜索卡片 -->
    <div class="glass-effect rounded-xl p-6 shadow-md mb-6">
        <form action="<?php echo e(route('admin.videos.index')); ?>" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <!-- 分类筛选 -->
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400"><?php echo e(__('admin.category')); ?></label>
                    <select name="category" onchange="this.form.submit()" class="block w-full rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm text-gray-900 dark:text-white ring-1 ring-inset ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200">
                        <option value=""><?php echo e(__('admin.all_categories')); ?></option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <!-- 处理状态筛选 -->
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400"><?php echo e(__('admin.processing_status')); ?></label>
                    <select name="status" onchange="this.form.submit()" class="block w-full rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm text-gray-900 dark:text-white ring-1 ring-inset ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200">
                        <option value=""><?php echo e(__('admin.all_statuses')); ?></option>
                        <option value="processed" <?php echo e(request('status') == 'processed' ? 'selected' : ''); ?>><?php echo e(__('admin.completed')); ?></option>
                        <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>><?php echo e(__('admin.processing')); ?></option>
                        <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>><?php echo e(__('admin.failed')); ?></option>
                    </select>
                </div>
                
                <!-- 使用状态筛选 -->
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400"><?php echo e(__('admin.usage_status')); ?></label>
                    <select name="usage_status" onchange="this.form.submit()" class="block w-full rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm text-gray-900 dark:text-white ring-1 ring-inset ring-gray-200 dark:ring-gray-700 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200">
                        <option value=""><?php echo e(__('admin.all_usage_statuses')); ?></option>
                        <option value="unused" <?php echo e(request('usage_status') == 'unused' ? 'selected' : ''); ?>><?php echo e(__('admin.unused')); ?></option>
                        <option value="used" <?php echo e(request('usage_status') == 'used' ? 'selected' : ''); ?>><?php echo e(__('admin.used')); ?></option>
                    </select>
                </div>
                
                <!-- 搜索框 -->
                <div class="sm:col-span-2 lg:col-span-2 space-y-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400"><?php echo e(__('admin.search')); ?></label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('admin.search_videos')); ?>" class="block w-full rounded-lg border-0 py-1.5 pl-7 pr-3 text-sm text-gray-900 dark:text-white ring-1 ring-inset ring-gray-200 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                        </div>
                        <!-- 搜索按钮 -->
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-3 py-1.5 text-sm font-medium text-white shadow hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 whitespace-nowrap">
                            <i class="fas fa-search mr-1"></i>
                            <?php echo e(__('admin.search')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 视频列表 -->
    <div class="glass-effect rounded-xl shadow-md overflow-hidden mb-6">
        <?php if($videos->count() > 0): ?>
            <!-- 批量操作区域 -->
            <div id="batchActions" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4 hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('admin.batch_actions')); ?>:</span>
                        <button onclick="batchUpdateUsage(1)" class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded-md hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 transition-colors duration-200">
                            <i class="fas fa-check mr-1.5 text-xs"></i>
                            <?php echo e(__('admin.mark_as_used')); ?>

                        </button>
                        <button onclick="batchUpdateUsage(0)" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 dark:bg-gray-900/30 dark:text-gray-400 dark:hover:bg-gray-900/50 transition-colors duration-200">
                            <i class="fas fa-times mr-1.5 text-xs"></i>
                            <?php echo e(__('admin.mark_as_unused')); ?>

                        </button>
                        <button onclick="batchDelete()" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-md hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-colors duration-200">
                            <i class="fas fa-trash mr-1.5 text-xs"></i>
                            <?php echo e(__('admin.batch_delete')); ?>

                        </button>
                    </div>
                    <button onclick="clearSelection()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <?php echo e(__('admin.clear_selection')); ?>

                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-video mr-1.5 text-xs"></i><?php echo e(__('admin.video_info')); ?>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-tag mr-1.5 text-xs"></i><?php echo e(__('admin.category')); ?>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-hdd mr-1.5 text-xs"></i><?php echo e(__('admin.file_size')); ?>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1.5 text-xs"></i><?php echo e(__('admin.processing_status')); ?>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-eye mr-1.5 text-xs"></i><?php echo e(__('admin.usage_status')); ?>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-clock mr-1.5 text-xs"></i><?php echo e(__('admin.upload_time')); ?>

                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-tools mr-1.5 text-xs"></i><?php echo e(__('admin.actions')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <!-- 选择框 -->
                            <td class="px-4 py-3">
                                <input type="checkbox" class="video-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500" value="<?php echo e($video->id); ?>">
                            </td>
                            <!-- 视频信息 -->
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-accent-500 rounded-lg flex items-center justify-center shadow-md">
                                            <i class="fas fa-video text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo e($video->title); ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e(basename($video->path)); ?></p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 分类 -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    <?php echo e($video->category->name ?? __('admin.uncategorized')); ?>

                                </span>
                            </td>
                            
                            <!-- 文件大小 -->
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-900 dark:text-white font-medium"><?php echo e(number_format($video->size / 1024 / 1024, 2)); ?> MB</span>
                            </td>
                            
                            <!-- 处理状态 -->
                            <td class="px-4 py-3">
                                <?php if($video->processed): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                        <?php echo e(__('admin.completed')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        <i class="fas fa-spinner fa-spin mr-1.5 text-xs"></i>
                                        <?php echo e(__('admin.processing')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- 使用状态 -->
                            <td class="px-4 py-3">
                                <?php if($video->is_used): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                        <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                        <?php echo e(__('admin.in_use')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300">
                                        <i class="fas fa-times-circle mr-1.5 text-xs"></i>
                                        <?php echo e(__('admin.not_used')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- 上传时间 -->
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?php echo e($video->created_at->format('Y-m-d')); ?>

                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo e($video->created_at->format('H:i:s')); ?>

                                </div>
                            </td>
                            
                            <!-- 操作按钮 -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <?php if($video->processed): ?>
                                        <!-- 预览按钮 -->
                                        <button @click="showPreview = true; videoUrl = '<?php echo e('/' . ltrim($video->path, '/')); ?>'" class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-indigo-100 text-indigo-600 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50 transition-colors duration-200 group" title="<?php echo e(__('admin.preview')); ?>">
                                            <i class="fas fa-eye text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                        </button>
                                        
                                        <!-- 下载按钮 -->
                                        <a href="<?php echo e(route('admin.videos.download', $video->id)); ?>" 
                                           class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-emerald-100 text-emerald-600 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50 transition-colors duration-200 group" 
                                           title="<?php echo e(__('admin.download')); ?>">
                                            <i class="fas fa-download text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                        </a>
                                    <?php else: ?>
                                        <!-- 处理中状态 -->
                                        <div class="flex items-center space-x-1.5 text-amber-600 dark:text-amber-400">
                                            <i class="fas fa-spinner fa-spin text-xs"></i>
                                            <span class="text-xs font-medium"><?php echo e(__('admin.processing')); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- 编辑按钮 -->
                                    <a href="<?php echo e(route('admin.videos.edit', $video)); ?>" 
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-sky-100 text-sky-600 hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-900/50 transition-colors duration-200 group" 
                                       title="<?php echo e(__('admin.edit')); ?>">
                                        <i class="fas fa-edit text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                    </a>
                                    
                                    <!-- 删除按钮 -->
                                    <button type="button" 
                                            onclick="deleteVideo(<?php echo e($video->id); ?>)"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-md bg-rose-100 text-rose-600 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors duration-200 group" 
                                            title="<?php echo e(__('admin.delete')); ?>">
                                        <i class="fas fa-trash text-xs group-hover:scale-110 transition-transform duration-200"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- 空状态 -->
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-film text-xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2"><?php echo e(__('admin.no_videos')); ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">还没有上传任何视频</p>
                <a href="<?php echo e(route('admin.videos.create')); ?>" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2 text-sm"></i>
                    <?php echo e(__('admin.upload_video')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 分页 -->
    <?php if($videos->hasPages()): ?>
        <div>
            <?php echo e($videos->links()); ?>

        </div>
    <?php endif; ?>

    <!-- 视频预览弹窗 -->
    <div x-show="showPreview" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-6 relative">
            <button @click="showPreview = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white"><?php echo e(__('admin.video_preview')); ?></h3>
            <video :src="videoUrl" controls class="w-full max-w-2xl max-h-[70vh] rounded-lg shadow mx-auto"></video>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.video-checkbox');
        const batchActions = document.getElementById('batchActions');

        function updateBatchActions() {
            const checked = document.querySelectorAll('.video-checkbox:checked').length;
            if (checked > 0) {
                batchActions.classList.remove('hidden');
            } else {
                batchActions.classList.add('hidden');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateBatchActions();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                // 如果有一个没选中，全选按钮也要取消
                if (!cb.checked) {
                    selectAll.checked = false;
                } else if (document.querySelectorAll('.video-checkbox:checked').length === checkboxes.length) {
                    selectAll.checked = true;
                }
                updateBatchActions();
            });
        });

        // 清除选择
        window.clearSelection = function () {
            checkboxes.forEach(cb => cb.checked = false);
            selectAll.checked = false;
            updateBatchActions();
        };

        // 批量更新使用状态
        window.batchUpdateUsage = function (isUsed) {
            const selectedIds = Array.from(document.querySelectorAll('.video-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('请选择要操作的视频');
                return;
            }
            fetch('<?php echo e(route('admin.videos.batch-update-usage')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ ids: selectedIds, is_used: isUsed })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || '操作失败');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('操作失败，请重试');
            });
        };

        // 批量删除
        window.batchDelete = function () {
            const selectedIds = Array.from(document.querySelectorAll('.video-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('请选择要删除的视频');
                return;
            }
            if (confirm('确定要删除选中的视频吗？此操作不可恢复。')) {
                fetch('<?php echo e(route('admin.videos.batch-delete')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || '删除失败');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('删除失败，请重试');
                });
            }
        };
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\phpstudy_pro\WWW\video-manager\resources\views/admin/videos/index.blade.php ENDPATH**/ ?>