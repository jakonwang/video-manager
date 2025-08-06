# 远程服务器部署指南

## 🚀 获取最新代码

### 方法一：直接拉取主分支（推荐）

```bash
# 1. 进入项目目录
cd /path/to/your/video-manager

# 2. 确保在正确的分支
git branch
# 应该显示: * main

# 3. 获取最新代码
git fetch origin

# 4. 重置到远程主分支
git reset --hard origin/main

# 5. 清理工作目录
git clean -fd
```

### 方法二：重新克隆项目

```bash
# 1. 备份当前配置（如果有）
cp .env .env.backup
cp storage/app/settings.json storage/app/settings.json.backup

# 2. 删除旧项目
cd ..
rm -rf video-manager

# 3. 重新克隆
git clone https://github.com/jakonwang/video-manager.git
cd video-manager

# 4. 恢复配置
cp ../video-manager-backup/.env .
cp ../video-manager-backup/storage/app/settings.json storage/app/
```

### 方法三：使用部署脚本

```bash
# 1. 下载部署脚本
wget https://raw.githubusercontent.com/jakonwang/video-manager/main/deploy.sh

# 2. 设置执行权限
chmod +x deploy.sh

# 3. 运行部署脚本
./deploy.sh
```

## ⚙️ 环境配置

### 1. 安装依赖

```bash
# 安装 PHP 依赖
composer install --no-dev --optimize-autoloader

# 安装前端依赖（如果需要）
npm install
npm run build
```

### 2. 环境变量配置

```bash
# 复制环境配置文件
cp .env.example .env

# 编辑环境配置
nano .env
```

#### 必要的环境变量：

```env
# 应用配置
APP_NAME="视频管理系统"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# 数据库配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=video_manager
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# 腾讯云 COS 配置（如果使用）
FILESYSTEM_DISK=cos
COS_SECRET_ID=your_secret_id
COS_SECRET_KEY=your_secret_key
COS_REGION=ap-beijing
COS_BUCKET=your-bucket-name
COS_DOMAIN=https://your-bucket.cos.ap-beijing.myqcloud.com
COS_TIMEOUT=60

# 队列配置
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. 生成应用密钥

```bash
php artisan key:generate
```

## 🗄️ 数据库设置

### 1. 运行数据库迁移

```bash
# 运行迁移
php artisan migrate --force

# 如果需要，运行数据填充
php artisan db:seed --force
```

### 2. 创建存储链接

```bash
php artisan storage:link
```

## 🔧 系统配置

### 1. 设置文件权限

```bash
# 设置目录权限
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 2. 清理缓存

```bash
# 清理各种缓存
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 3. 优化生产环境

```bash
# 缓存配置
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🚀 启动服务

### 1. 启动队列处理器

```bash
# 开发环境
php artisan queue:work

# 生产环境（后台运行）
nohup php artisan queue:work --daemon > /dev/null 2>&1 &

# 使用 Supervisor（推荐）
sudo nano /etc/supervisor/conf.d/video-manager.conf
```

#### Supervisor 配置示例：

```ini
[program:video-manager-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/video-manager/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/video-manager/storage/logs/worker.log
stopwaitsecs=3600
```

### 2. 配置 Web 服务器

#### Nginx 配置示例：

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/video-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # 文件上传大小限制
    client_max_body_size 1024m;
}
```

## ✅ 验证部署

### 1. 基础功能检查

```bash
# 检查应用状态
php artisan videos:check-status

# 检查队列状态
php artisan queue:failed

# 检查存储状态
php artisan storage:link
```

### 2. 访问测试

- [ ] 网站首页可以正常访问
- [ ] 管理后台可以正常登录
- [ ] 文件上传功能正常
- [ ] 数据库连接正常

### 3. 腾讯云 COS 测试

- [ ] 访问 `/admin/settings` 配置页面
- [ ] 测试 COS 连接功能
- [ ] 上传测试视频文件
- [ ] 下载测试视频文件

## 🔍 故障排除

### 常见问题

#### 1. 权限问题
```bash
# 重新设置权限
sudo chown -R www-data:www-data /path/to/video-manager
sudo chmod -R 755 /path/to/video-manager/storage
```

#### 2. 数据库连接失败
```bash
# 检查数据库配置
php artisan tinker
DB::connection()->getPdo();
```

#### 3. 队列不工作
```bash
# 检查队列状态
php artisan queue:failed
php artisan queue:restart
```

#### 4. 文件上传失败
```bash
# 检查 PHP 配置
php -i | grep upload_max_filesize
php -i | grep post_max_size
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

```bash
# 清理临时文件
php artisan log:clear

# 备份数据库
php artisan backup:run

# 更新依赖
composer update --no-dev
```

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

---

**注意**: 部署前请务必备份重要数据，并在测试环境中先验证功能正常。 