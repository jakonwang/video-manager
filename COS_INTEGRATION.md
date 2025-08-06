# 腾讯云 COS 存储集成说明

## 概述

本项目已完整集成腾讯云 COS 对象存储服务，支持视频文件的上传、下载和预览功能。

## 功能特性

### ✅ 已完成功能

1. **视频上传**
   - 支持大文件分片上传（>100MB）
   - 异步队列处理
   - 自动文件类型检测
   - 内存优化的流式处理

2. **视频下载**
   - 从 COS 获取文件内容
   - 支持断点续传
   - 自动文件类型识别
   - 下载权限控制

3. **视频预览**
   - 流媒体播放支持
   - 范围请求处理
   - 支持视频快进和跳转

4. **管理后台**
   - COS 配置管理界面
   - 实时连接测试
   - 环境变量自动更新
   - 配置验证和错误处理

## 技术架构

### 核心组件

1. **CosAdapter** (`app/Services/CosAdapter.php`)
   - 封装腾讯云 COS SDK
   - 提供统一的文件操作接口
   - 支持上传、下载、删除等操作

2. **CosServiceProvider** (`app/Providers/CosServiceProvider.php`)
   - Laravel 服务提供者
   - 注册 COS 存储驱动
   - 配置依赖注入

3. **队列任务** (`app/Jobs/ProcessVideoUpload.php`)
   - 异步处理视频上传
   - 大文件分片上传
   - 错误处理和重试机制

### 配置说明

#### 环境变量配置

在 `.env` 文件中添加以下配置：

```env
# 文件系统配置
FILESYSTEM_DISK=cos

# 腾讯云 COS 配置
COS_SECRET_ID=your_secret_id_here
COS_SECRET_KEY=your_secret_key_here
COS_REGION=ap-beijing
COS_BUCKET=your_bucket_name
COS_DOMAIN=https://your-bucket.cos.ap-beijing.myqcloud.com
COS_TIMEOUT=60
```

#### 文件系统配置

`config/filesystems.php` 中已添加 COS 配置：

```php
'cos' => [
    'driver' => 'cos',
    'app_id' => env('COS_APP_ID'),
    'secret_id' => env('COS_SECRET_ID'),
    'secret_key' => env('COS_SECRET_KEY'),
    'region' => env('COS_REGION', 'ap-beijing'),
    'bucket' => env('COS_BUCKET'),
    'domain' => env('COS_DOMAIN'),
    'scheme' => env('COS_SCHEME', 'https'),
    'timeout' => env('COS_TIMEOUT', 60),
],
```

## 使用指南

### 1. 配置腾讯云 COS

1. 登录腾讯云控制台
2. 创建对象存储 COS 存储桶
3. 获取 Secret ID 和 Secret Key
4. 在管理后台配置 COS 信息

### 2. 管理后台配置

访问 `/admin/settings` 页面：

1. 勾选"启用腾讯云 COS 存储"
2. 填写配置信息：
   - Secret ID
   - Secret Key
   - 存储桶地域
   - 存储桶名称
3. 点击"测试连接"验证配置
4. 保存配置

### 3. 视频上传

1. 访问 `/admin/videos/create`
2. 选择视频文件和填写信息
3. 系统自动上传到腾讯云 COS
4. 后台队列处理上传任务

### 4. 视频下载

- **管理后台**：`/admin/videos/{id}/download`
- **移动端**：`/mobile/category/{categoryId}/video`

### 5. 视频预览

- **移动端**：`/mobile/video/{id}/preview`

## API 接口

### 测试 COS 连接

```http
POST /admin/settings/test-cos
Content-Type: application/json

{
    "cos_secret_id": "your_secret_id",
    "cos_secret_key": "your_secret_key",
    "cos_region": "ap-beijing",
    "cos_bucket": "your_bucket_name"
}
```

### 响应示例

```json
{
    "success": true,
    "message": "腾讯云 COS 连接测试成功！存储桶访问权限正常。"
}
```

## 错误处理

### 常见错误及解决方案

1. **网络连接失败**
   - 检查网络连接
   - 确认防火墙设置
   - 验证地域配置

2. **认证失败**
   - 检查 Secret ID 和 Secret Key
   - 确认账号权限
   - 验证存储桶权限

3. **存储桶不存在**
   - 检查存储桶名称
   - 确认存储桶地域
   - 验证存储桶权限

4. **文件上传失败**
   - 检查文件大小限制
   - 确认存储桶空间
   - 验证网络连接

## 性能优化

### 大文件处理

- 大于 100MB 的文件自动使用分片上传
- 支持断点续传
- 内存优化的流式处理

### 缓存策略

- 文件元数据缓存
- 连接池复用
- 智能重试机制

## 安全考虑

1. **密钥管理**
   - 使用环境变量存储敏感信息
   - 定期轮换密钥
   - 最小权限原则

2. **访问控制**
   - 存储桶权限设置
   - 防盗链配置
   - IP 白名单

3. **数据安全**
   - HTTPS 传输
   - 文件加密存储
   - 访问日志记录

## 监控和日志

### 日志记录

系统会自动记录以下操作：

- 文件上传/下载操作
- 连接测试结果
- 错误信息和异常
- 性能指标

### 监控指标

- 上传成功率
- 下载速度
- 存储使用量
- 错误率统计

## 故障排除

### 检查步骤

1. **验证配置**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **测试连接**
   - 使用管理后台测试功能
   - 检查网络连接
   - 验证存储桶权限

3. **查看日志**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **检查队列**
   ```bash
   php artisan queue:work
   ```

## 更新日志

### v1.0.0 (当前版本)
- ✅ 完整的 COS 存储集成
- ✅ 大文件上传支持
- ✅ 流媒体播放支持
- ✅ 管理后台配置界面
- ✅ 实时连接测试
- ✅ 错误处理和日志记录

## 技术支持

如有问题，请：

1. 查看日志文件：`storage/logs/laravel.log`
2. 检查腾讯云 COS 控制台
3. 运行诊断命令：`php artisan videos:check-status`
4. 联系技术支持

---

**注意**：请确保在生产环境中正确配置所有安全设置，并定期备份重要数据。 