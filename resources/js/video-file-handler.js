// 文件处理模块
window.videoFileHandler = {
    files: [],
    uploadProgress: {},
    uploadError: null,

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

    handleFileSelect(event) {
        this.uploadError = null;
        const newFiles = Array.from(event.target.files || event.dataTransfer.files);
        
        newFiles.forEach(file => {
            if (!this.validateFile(file)) {
                return;
            }

            // 检查是否已存在相同文件名的文件
            if (!this.files.some(f => f.name === file.name)) {
                this.uploadProgress[file.name] = 0;
                this.files.push(file);
            } else {
                this.uploadError = `文件 ${file.name} 已添加`;
            }
        });

        // 清空文件输入框，允许重复选择相同文件
        if (event.target.value) {
            event.target.value = '';
        }
    },

    removeFile(index) {
        if (this.uploading) {
            return;
        }
        const file = this.files[index];
        delete this.uploadProgress[file.name];
        this.files.splice(index, 1);
        this.uploadError = null;
    },

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
};