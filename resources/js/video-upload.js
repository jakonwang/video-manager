// 视频上传处理函数
window.handleVideoUpload = function(files, options = {}) {
    const {
        onProgress = () => {},
        onSuccess = () => {},
        onError = () => {},
        uploadUrl,
        csrfToken,
        formData = {}
    } = options;

    if (files.length === 0) {
        onError('请选择要上传的视频文件');
        return;
    }

    if (!formData.title || formData.title.trim() === '') {
        onError('请输入视频标题');
        return;
    }

    if (!formData.category_id) {
        onError('请选择视频分类');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', uploadUrl, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Accept', 'application/json');

    // 处理上传进度
    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const progress = (e.loaded / e.total) * 100;
            onProgress(progress);
        }
    };

    // 处理上传完成
    xhr.onload = () => {
        try {
            const response = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && response.success) {
                onSuccess(response);
            } else if (xhr.status === 422) {
                // 处理验证错误
                onError(response);
            } else {
                onError(response.message || `上传失败：${xhr.status} ${xhr.statusText}`);
            }
        } catch (error) {
            console.error('解析响应失败:', error);
            onError('上传失败：服务器响应格式错误');
        }
    };

    // 处理网络错误
    xhr.onerror = () => {
        onError('上传失败：网络错误');
    };

    // 处理超时
    xhr.ontimeout = () => {
        onError('上传失败：请求超时');
    };

    // 准备表单数据
    const data = new FormData();
    files.forEach(file => {
        data.append('videos[]', file);
    });
    
    // 添加其他表单数据
    Object.entries(formData).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            data.append(key, value.toString().trim());
        }
    });

    // 设置超时时间（5分钟）
    xhr.timeout = 300000;

    // 发送请求
    try {
        xhr.send(data);
    } catch (error) {
        console.error('发送请求失败:', error);
        onError('上传失败：发送请求时出错');
    }

    return xhr;
};