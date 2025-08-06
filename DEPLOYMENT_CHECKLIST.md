# 线上部署检查清单

## 🚀 部署前准备

### 1. 环境要求检查
- [ ] PHP >= 8.2
- [ ] Composer 已安装
- [ ] MySQL/MariaDB 数据库
- [ ] Redis（用于队列）
- [ ] 必要的 PHP 扩展：curl, json, xml, mbstring

### 2. 腾讯云 COS 准备
- [ ] 腾讯云账号已注册
- [ ] 对象存储 COS 服务已开通
- [ ] 存储桶已创建
- [ ] Secret ID 和 Secret Key 已获取
- [ ] 存储桶权限已配置

## 📥 代码部署

### 方法一：Git 更新（推荐）
```bash
# 1. 进入项目目录
cd /path/to/your/project

# 2. 获取最新代码
git fetch origin
git checkout clean-cos-integration-v2
git pull origin clean-cos-integration-v2

# 3. 安装依赖
composer install --no-dev --optimize-autoloader

# 4. 清理缓存
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 方法二：使用部署脚本
```bash
# Linux/Mac
chmod +x deploy.sh
./deploy.sh

# Windows
deploy.bat
```

## ⚙️ 环境配置

### 1. 数据库配置
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. 腾讯云 COS 配置
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

### 3. 队列配置
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🔧 系统配置

### 1. 文件权限设置
```bash
# Linux/Mac
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

# Windows (以管理员身份运行)
icacls storage /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls bootstrap\cache /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 2. 数据库迁移
```bash
php artisan migrate --force
```

### 3. 生成应用密钥
```bash
php artisan key:generate
```

## 🚀 服务启动

### 1. 启动队列处理器
```bash
# 开发环境
php artisan queue:work

# 生产环境（后台运行）
nohup php artisan queue:work --daemon > /dev/null 2>&1 &

# 使用 Supervisor（推荐）
# 配置 supervisor 管理队列进程
```

### 2. 启动 Web 服务器
```bash
# 使用 Laravel 内置服务器（开发环境）
php artisan serve

# 使用 Nginx/Apache（生产环境）
# 配置 Web 服务器指向 public 目录
```

## ✅ 部署后检查

### 1. 基础功能检查
- [ ] 网站首页可以正常访问
- [ ] 管理后台可以正常登录
- [ ] 数据库连接正常
- [ ] 文件上传功能正常

### 2. 腾讯云 COS 功能检查
- [ ] 访问 `/admin/settings` 配置页面
- [ ] 测试 COS 连接功能
- [ ] 上传测试视频文件
- [ ] 下载测试视频文件
- [ ] 预览测试视频文件

### 3. 队列功能检查
- [ ] 队列处理器正在运行
- [ ] 视频上传任务正常处理
- [ ] 队列日志正常记录

### 4. 性能检查
- [ ] 页面加载速度正常
- [ ] 文件上传速度正常
- [ ] 内存使用情况正常
- [ ] 磁盘空间充足

## 🔍 故障排除

### 常见问题及解决方案

#### 1. 数据库连接失败
```bash
# 检查数据库配置
php artisan tinker
DB::connection()->getPdo();
```

#### 2. COS 连接失败
- 检查网络连接
- 验证 Secret ID 和 Secret Key
- 确认存储桶权限
- 查看错误日志：`storage/logs/laravel.log`

#### 3. 队列处理器不工作
```bash
# 检查队列状态
php artisan queue:failed

# 重启队列
php artisan queue:restart

# 检查 Redis 连接
php artisan tinker
Redis::ping();
```

#### 4. 文件权限问题
```bash
# 检查文件权限
ls -la storage/
ls -la bootstrap/cache/

# 重新设置权限
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 📊 监控和维护

### 1. 日志监控
```bash
# 查看应用日志
tail -f storage/logs/laravel.log

# 查看队列日志
tail -f storage/logs/queue.log
```

### 2. 性能监控
- 监控服务器 CPU 和内存使用
- 监控磁盘空间使用
- 监控网络带宽使用
- 监控数据库性能

### 3. 定期维护
- 清理临时文件
- 备份数据库
- 更新系统安全补丁
- 检查日志文件大小

## 🆘 紧急回滚

如果部署出现问题，可以快速回滚：

```bash
# 1. 停止当前服务
php artisan down

# 2. 回滚到备份版本
cd /path/to/backup
cp -r * /path/to/production/

# 3. 清理缓存
php artisan config:clear
php artisan cache:clear

# 4. 重启服务
php artisan up
```

## 📞 技术支持

如果遇到问题，请：

1. 查看错误日志：`storage/logs/laravel.log`
2. 检查系统状态：`php artisan videos:check-status`
3. 查看队列状态：`php artisan queue:failed`
4. 联系技术支持

---

**注意**：部署前请务必备份重要数据，并在测试环境中先验证功能正常。 