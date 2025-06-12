@extends('admin.layouts.app')

@section('title', __('admin.upload_video'))

@section('content')
<div class="space-y-4 animate-fade-in">
    <!-- 页面头部 -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 shadow-md">
                <i class="fas fa-cloud-upload-alt text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    {{ __('admin.upload_video') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">支持多文件拖拽上传，队列处理，实时进度显示</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <div class="glass-effect rounded-md px-2 py-1">
                <span class="text-xs text-gray-500 dark:text-gray-400">支持格式:</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white ml-1">MP4, AVI, MOV</span>
            </div>
            <div class="glass-effect rounded-md px-2 py-1">
                <span class="text-xs text-gray-500 dark:text-gray-400">最大:</span>
                <span class="text-xs font-medium text-gray-900 dark:text-white ml-1">2GB</span>
            </div>
        </div>
    </div>

    <div x-data="videoUploadHandler()" x-init="init()" class="space-y-4">
        <!-- 上传区域 -->
        <div class="glass-effect rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <!-- 基本信息表单 -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-video text-primary-500 mr-1.5 text-xs"></i>视频标题
                        </label>
                        <input type="text" 
                            id="title" 
                            x-model="title"
                            class="w-full px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            placeholder="请输入视频标题">
                        <div x-show="errors.title" class="mt-1 text-xs text-red-600 dark:text-red-400" x-text="errors.title"></div>
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-folder text-primary-500 mr-1.5 text-xs"></i>视频分类
                        </label>
                        <select x-model="category_id" 
                            id="category_id"
                            class="w-full px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                            <option value="">请选择分类</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div x-show="errors.category_id" class="mt-1 text-xs text-red-600 dark:text-red-400" x-text="errors.category_id"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-align-left text-primary-500 mr-1.5 text-xs"></i>视频描述
                    </label>
                    <textarea x-model="description" 
                        id="description"
                        rows="2"
                        class="w-full px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 resize-none"
                        placeholder="请输入视频描述（可选）"></textarea>
                </div>

                <!-- 文件上传区域 -->
                <div class="relative">
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center transition-all duration-300"
                        :class="{
                            'border-primary-400 bg-primary-50 dark:bg-primary-900/20': isDragging,
                            'border-gray-300 dark:border-gray-600': !isDragging
                        }"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)">
                        
                        <div class="space-y-4">
                            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                            </div>
                            
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">拖拽文件到此处或点击选择</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">支持批量选择多个视频文件</p>
                                
                                <label for="video-files" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 cursor-pointer shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm">
                                    <i class="fas fa-plus mr-2"></i>
                                    选择视频文件
                                </label>
                                <input type="file" 
                                    id="video-files"
                                    multiple 
                                    accept="video/*"
                                    class="hidden"
                                    @change="handleFileSelect($event)">
                            </div>
                        </div>
                    </div>
                    
                    <!-- 错误信息 -->
                    <div x-show="errorMessage" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="text-sm text-red-700 dark:text-red-400" x-text="errorMessage"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 文件列表和上传队列 -->
        <div x-show="files.length > 0" class="glass-effect rounded-lg shadow-lg overflow-hidden">
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-list text-primary-500 mr-2"></i>
                            上传队列
                        </h3>
                        <span class="bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-200 px-2 py-0.5 rounded-full text-xs font-medium" x-text="files.length + ' 个文件'"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" 
                            @click="clearAllFiles()"
                            :disabled="uploading"
                            class="px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200 disabled:opacity-50">
                            <i class="fas fa-trash mr-1"></i>
                            清空队列
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="max-h-72 overflow-y-auto">
                <template x-for="(file, index) in files" :key="file.name + index">
                    <div class="p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 flex-1">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-video text-white text-sm"></i>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="file.name"></h4>
                                    <div class="flex items-center space-x-3 mt-0.5">
                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formatFileSize(file.size)"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="file.type"></span>
                                        <div class="flex items-center space-x-1">
                                            <div class="w-1.5 h-1.5 rounded-full" 
                                                :class="{
                                                    'bg-gray-400': !uploadProgress[file.name],
                                                    'bg-yellow-400 animate-pulse': uploadProgress[file.name] > 0 && uploadProgress[file.name] < 100,
                                                    'bg-green-500': uploadProgress[file.name] === 100
                                                }"></div>
                                            <span class="text-xs font-medium"
                                                :class="{
                                                    'text-gray-500': !uploadProgress[file.name],
                                                    'text-yellow-600': uploadProgress[file.name] > 0 && uploadProgress[file.name] < 100,
                                                    'text-green-600': uploadProgress[file.name] === 100
                                                }"
                                                x-text="uploadProgress[file.name] ? (uploadProgress[file.name] === 100 ? '已完成' : '上传中') : '等待中'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" 
                                @click="removeFile(index)"
                                :disabled="uploading"
                                class="ml-3 p-1.5 text-gray-400 hover:text-red-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-times text-base"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 上传状态显示 -->
        <div x-show="files.length > 0" class="glass-effect rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-line text-primary-500 mr-2"></i>
                    上传状态
                </h3>
            </div>
            
            <div class="p-4">
                <!-- 全局上传进度条 -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <span class="font-medium text-gray-700 dark:text-gray-300">总体进度</span>
                            <span class="ml-2 px-3 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-200 rounded-full" x-text="Math.round(getAverageProgress()) + '%'"></span>
                        </div>
                        <span id="upload-status" class="text-sm font-medium text-gray-600 dark:text-gray-400">等待开始上传...</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-primary-600 rounded-full transition-all duration-300 ease-out"
                            :style="'width: ' + getAverageProgress() + '%'"
                            :class="{ 'animate-pulse': uploading }"></div>
                    </div>
                </div>
                
                <!-- 上传统计 -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="files.length"></div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">总文件数</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="getCompletedCount()"></div>
                        <div class="text-sm text-green-600 dark:text-green-400">已完成</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" x-text="getUploadingCount()"></div>
                        <div class="text-sm text-yellow-600 dark:text-yellow-400">上传中</div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="getTotalSize()"></div>
                        <div class="text-sm text-blue-600 dark:text-blue-400">总大小</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 上传控制 -->
        <div x-show="files.length > 0" class="glass-effect rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div x-show="!uploading" class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                        准备就绪，点击右侧按钮开始上传
                    </div>
                    <div x-show="uploading" class="text-sm text-primary-600 dark:text-primary-400 animate-pulse">
                        <i class="fas fa-sync-alt fa-spin mr-1"></i>
                        正在处理上传队列，请勿关闭页面...
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.videos.index') }}" 
                        :class="{'opacity-50 cursor-not-allowed': uploading}"
                        class="inline-flex items-center px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 font-semibold text-lg shadow-sm">
                        <i class="fas fa-arrow-left mr-3"></i>
                        返回列表
                    </a>
                    
                    <button type="button" 
                        @click="startBatchUpload()"
                        :disabled="uploading || files.length === 0 || !title || !category_id"
                        class="inline-flex items-center px-10 py-4 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-bold rounded-xl hover:from-primary-600 hover:to-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-lg">
                        <template x-if="!uploading">
                            <div class="flex items-center">
                                <i class="fas fa-rocket mr-3"></i>
                                开始批量上传
                            </div>
                        </template>
                        <template x-if="uploading">
                            <div class="flex items-center">
                                <i class="fas fa-spinner fa-spin mr-3"></i>
                                上传中...
                            </div>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.videoUploadHandler = function() {
    return {
        // 基础状态
        files: [],
        uploading: false,
        uploadProgress: {},
        errors: {},
        errorMessage: '',
        isDragging: false,
        uploadQueue: [],
        currentUploadIndex: 0,
        maxConcurrentUploads: 3,
        
        // 表单数据
        title: '',
        description: '',
        category_id: '',
        
        // 初始化
        init() {
            console.log('视频上传处理器初始化');
        },
        
        // 文件处理方法
        handleFileSelect(event) {
            this.errorMessage = '';
            const newFiles = Array.from(event.target.files || event.dataTransfer.files);
            
            newFiles.forEach(file => {
                if (!this.validateFile(file)) {
                    return;
                }

                // 检查是否已存在相同文件名的文件
                if (!this.files.some(f => f.name === file.name)) {
                    this.files.push(file);
                    this.uploadProgress[file.name] = 0;
                } else {
                    this.errorMessage = `文件 ${file.name} 已添加`;
                }
            });

            // 清空文件输入框
            if (event.target.value) {
                event.target.value = '';
            }
        },
        
        handleDrop(event) {
            this.isDragging = false;
            this.handleFileSelect(event);
        },
        
        removeFile(index) {
            if (this.uploading) {
                return;
            }
            const file = this.files[index];
            delete this.uploadProgress[file.name];
            this.files.splice(index, 1);
            this.errorMessage = '';
        },
        
        clearAllFiles() {
            if (this.uploading) {
                return;
            }
            this.files = [];
            this.uploadProgress = {};
            this.errorMessage = '';
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        validateFile(file) {
            // 检查文件类型
            if (!file.type.startsWith('video/')) {
                this.errorMessage = `文件 ${file.name} 不是有效的视频文件`;
                return false;
            }

            // 检查文件大小（最大500MB）
            const maxSize = 500 * 1024 * 1024; // 500MB in bytes
            if (file.size > maxSize) {
                this.errorMessage = `文件 ${file.name} 超过最大限制（500MB）`;
                return false;
            }

            return true;
        },

        validateForm() {
            this.errors = {};
            let isValid = true;

            // 验证标题
            if (!this.title || !this.title.trim()) {
                this.errors.title = '请输入视频标题';
                isValid = false;
            }

            // 验证分类
            if (!this.category_id) {
                this.errors.category_id = '请选择视频分类';
                isValid = false;
            }

            // 验证文件
            if (this.files.length === 0) {
                this.errorMessage = '请选择要上传的视频文件';
                isValid = false;
            }

            return isValid;
        },
        
        // 统计方法
        getCompletedCount() {
            return Object.values(this.uploadProgress).filter(progress => progress === 100).length;
        },
        
        getUploadingCount() {
            return Object.values(this.uploadProgress).filter(progress => progress > 0 && progress < 100).length;
        },
        
        getTotalSize() {
            const totalBytes = this.files.reduce((sum, file) => sum + file.size, 0);
            return this.formatFileSize(totalBytes);
        },
        
        getAverageProgress() {
            if (this.files.length === 0) return 0;
            
            const totalProgress = Object.values(this.uploadProgress).reduce((sum, progress) => sum + (progress || 0), 0);
            return Math.round(totalProgress / this.files.length);
        },

        // 批量上传处理
        async startBatchUpload() {
            if (!this.validateForm()) {
                return;
            }

            this.uploading = true;
            this.errors = {};
            this.errorMessage = '';
            this.currentUploadIndex = 0;

            // 重置所有文件的进度
            this.files.forEach(file => {
                this.uploadProgress[file.name] = 0;
            });

            try {
                // 创建上传队列
                this.uploadQueue = [...this.files];
                
                // 开始并发上传
                const uploadPromises = [];
                const concurrentCount = Math.min(this.maxConcurrentUploads, this.uploadQueue.length);
                
                for (let i = 0; i < concurrentCount; i++) {
                    uploadPromises.push(this.processUploadQueue());
                }
                
                await Promise.all(uploadPromises);
                
                // 所有上传完成
                setTimeout(() => {
                    window.location.href = '{{ route("admin.videos.index") }}';
                }, 2000);
                
            } catch (error) {
                console.error('批量上传失败:', error);
                this.errorMessage = '批量上传失败，请重试';
                this.uploading = false;
            }
        },
        
        async processUploadQueue() {
            while (this.currentUploadIndex < this.uploadQueue.length && this.uploading) {
                const fileIndex = this.currentUploadIndex++;
                if (fileIndex >= this.uploadQueue.length) break;
                
                const file = this.uploadQueue[fileIndex];
                await this.uploadSingleFile(file);
            }
        },
        
        async uploadSingleFile(file) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('videos[]', file);
                formData.append('title', this.title.trim());
                formData.append('description', this.description ? this.description.trim() : '');
                formData.append('category_id', this.category_id);
                
                const xhr = new XMLHttpRequest();
                
                // 上传进度
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        this.uploadProgress[file.name] = Math.round(percentComplete);
                    }
                });
                
                // 上传完成
                xhr.addEventListener('load', () => {
                    console.log(`文件 ${file.name} 上传响应:`, xhr.status, xhr.responseText);
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log(`文件 ${file.name} 解析响应:`, response);
                            if (response.success) {
                                this.uploadProgress[file.name] = 100;
                                resolve(response);
                            } else {
                                console.error(`文件 ${file.name} 上传失败:`, response.message);
                                throw new Error(response.message || '上传失败');
                            }
                        } catch (error) {
                            console.error(`文件 ${file.name} 响应解析错误:`, error);
                            this.uploadProgress[file.name] = -1; // 标记为失败
                            reject(error);
                        }
                    } else {
                        console.error(`文件 ${file.name} HTTP错误:`, xhr.status, xhr.statusText);
                        this.uploadProgress[file.name] = -1; // 标记为失败
                        reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
                    }
                });
                
                // 上传错误
                xhr.addEventListener('error', () => {
                    this.uploadProgress[file.name] = -1; // 标记为失败
                    reject(new Error('网络错误'));
                });
                
                // 设置请求
                xhr.open('POST', '{{ route("admin.videos.store") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                // 发送请求
                xhr.send(formData);
            });
        }
    };
};
</script>
@endpush
@endsection