// 视频文件上传处理模块
window.videoUploadHandler = function() {
    return {
        // 基础状态
        files: [],
        uploading: false,
        uploadProgress: {},
        errors: {},
        errorMessage: '',
        isDragging: false,
        
        // 表单数据
        title: '',
        description: '',
        category_id: '',
        
        // 初始化
        init() {
            // 合并videoFileHandler的方法
            Object.assign(this, window.videoFileHandler);
        },
        
        // 文件处理方法
        handleFileSelect(event) {
            this.errorMessage = '';
            const newFiles = Array.from(event.target.files || event.dataTransfer.files);
            
            newFiles.forEach(file => {
                if (!this.validateFile(file)) {
                    this.errorMessage = this.uploadError;
                    return;
                }

                // 检查是否已存在相同文件名的文件
                if (!this.files.some(f => f.name === file.name)) {
                    file.progress = null;
                    this.files.push(file);
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
            this.files.splice(index, 1);
            this.errorMessage = '';
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        validateFile(file) {
            // 检查文件类型
            if (!file.type.startsWith('video/')) {
                this.uploadError = `文件 ${file.name} 不是有效的视频文件`;
                return false;
            }

            // 检查文件大小（最大2GB）
            const maxSize = 2 * 1024 * 1024 * 1024; // 2GB in bytes
            if (file.size > maxSize) {
                this.uploadError = `文件 ${file.name} 超过最大限制（2GB）`;
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
                this.errors.videos = '请选择要上传的视频文件';
                isValid = false;
            }

            return isValid;
        },

        resetUpload() {
            this.files = [];
            this.uploading = false;
            this.uploadProgress = {};
            this.errors = {};
            this.errorMessage = '';
            this.title = '';
            this.description = '';
            this.category_id = '';
        },

        async startUpload() {
            if (!this.validateForm()) {
                return;
            }

            this.uploading = true;
            this.errors = {};
            this.errorMessage = '';

            // 重置进度
            this.files.forEach(file => {
                file.progress = 0;
            });

            try {
                const formData = {
                    title: this.title.trim(),
                    description: this.description ? this.description.trim() : '',
                    category_id: this.category_id
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF令牌未找到');
                }

                const uploadUrl = '/videos';

                window.handleVideoUpload(this.files, {
                    uploadUrl: uploadUrl,
                    csrfToken: csrfToken.content,
                    formData: formData,
                    onProgress: (progress) => {
                        this.files.forEach(file => {
                            file.progress = Math.round(progress);
                        });
                        document.getElementById('upload-status')?.textContent = `上传进度: ${Math.round(progress)}%`;
                    },
                    onSuccess: (response) => {
                        const successCount = response.data?.success_count || 0;
                        const failCount = response.data?.fail_count || 0;
                        const totalCount = successCount + failCount;
                        
                        let message = '';
                        if (failCount > 0) {
                            message = `${successCount}/${totalCount} 个视频已成功加入处理队列，${failCount} 个视频上传失败。`;
                        } else {
                            message = `${successCount} 个视频已成功加入处理队列，后台正在处理中。`;
                        }
                        
                        if (document.getElementById('upload-status')) {
                            document.getElementById('upload-status').textContent = '上传完成，视频已加入处理队列';
                        }
                        
                        // 延迟跳转，让用户看到100%的进度
                        setTimeout(() => {
                            window.location.href = '/admin/videos';
                        }, 2000);
                    },
                    onError: (error) => {
                        if (typeof error === 'object' && error.errors) {
                            // 处理验证错误
                            this.errors = error.errors;
                        } else {
                            // 处理一般错误
                            this.errorMessage = typeof error === 'string' ? error : '上传失败，请重试';
                        }
                        this.uploading = false;
                    }
                });
            } catch (error) {
                this.errorMessage = error.message;
                this.uploading = false;
            }
        }
    };
};