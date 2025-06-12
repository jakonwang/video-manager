# 视频管理系统 (Video Manager)

一个基于 Laravel 11.x 的专业视频管理系统，专注于视频资源的管理、分发和数据统计。支持中文、英文和越南语三种语言。

## 主要功能

### 1. 仪表盘
- **数据概览**
  * 视频总数统计
  * 已处理视频统计
  * 分类总数统计
  * 已使用视频统计
  * 存储空间使用统计
  * 今日数据统计

- **分类统计**
  * 分类视频数量统计
  * 分类使用率统计
  * TOP5 热门分类展示
  * 分类数据可视化图表

- **趋势分析**
  * 近30天上传/下载趋势图
  * 数据实时更新
  * 趋势数据可视化

### 2. 视频管理
- **视频上传**
  * 支持多种视频格式（MP4、AVI、MOV、WMV、FLV、WEBM）
  * 批量上传功能
  * 上传进度实时显示
  * 自动视频处理

- **视频列表**
  * 视频信息展示
  * 视频分类管理
  * 视频状态管理
  * 视频预览功能
  * 视频下载功能

- **视频操作**
  * 编辑视频信息
  * 更改视频分类
  * 标记使用状态
  * 批量操作功能
  * 删除视频

### 3. 分类管理
- **分类操作**
  * 创建新分类
  * 编辑分类信息
  * 设置分类排序
  * 分类状态控制
  * 删除分类

- **分类数据**
  * 分类视频数量
  * 分类使用统计
  * 分类下载统计
  * 分类增长趋势

### 4. 用户管理
- **用户操作**
  * 添加新用户
  * 编辑用户信息
  * 设置用户角色
  * 修改用户密码
  * 用户状态管理

- **角色权限**
  * 管理员角色
  * 编辑者角色
  * 自定义权限配置

### 5. 系统设置
- **基础设置**
  * 站点信息配置
  * 语言设置
  * 主题切换
  * 时区设置

- **存储设置**
  * 存储路径配置
  * 文件类型限制
  * 上传大小限制

### 6. 多语言支持
- 支持中文、英文、越南语
- 一键切换语言
- 完整的语言包
- 自动语言检测

## 安装步骤

### 1. 系统要求
- PHP >= 8.2
- MySQL >= 8.0
- Composer 2.x
- Node.js >= 18.x
- NPM >= 9.x
- Redis >= 7.x（可选，用于缓存）

### 2. 安装过程

1. 克隆项目：
```bash
git clone https://github.com/yourusername/video-manager.git
cd video-manager
```

2. 安装 PHP 依赖：
```bash
composer install
```

3. 安装前端依赖：
```bash
npm install
npm run build
```

4. 环境配置：
```bash
cp .env.example .env
php artisan key:generate
```

5. 配置数据库：
编辑 .env 文件，设置数据库连接信息：
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=video_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. 运行数据库迁移：
```bash
php artisan migrate --seed
```

7. 创建存储链接：
```bash
php artisan storage:link
```

8. 设置目录权限：
```bash
# Linux系统
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows系统
# 确保 IIS/Apache 用户对 storage 和 bootstrap/cache 目录有写入权限
```

### 3. 默认管理员账号
- 邮箱：admin@example.com
- 密码：password

**重要提示：** 首次登录后请立即修改默认密码！

## 常见问题解决方案

### 1. 安装相关问题

#### Composer 安装失败
- **问题**: 依赖安装失败
- **解决方案**: 
  ```bash
  composer clear-cache
  composer install --ignore-platform-reqs
  ```

#### NPM 构建失败
- **问题**: Node 模块安装或构建失败
- **解决方案**:
  ```bash
  rm -rf node_modules
  npm cache clean --force
  npm install
  npm run build
  ```

### 2. 运行时问题

#### 500 服务器错误
- **解决方案**:
  1. 检查存储权限
  2. 检查 .env 文件配置
  3. 清理缓存：
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan view:clear
  ```

#### 视频上传失败
- **解决方案**:
  1. 检查 php.ini 配置：
  ```ini
  upload_max_filesize = 1024M
  post_max_size = 1024M
  max_execution_time = 300
  memory_limit = 512M
  ```
  2. 检查存储目录权限
  3. 确保存储链接已创建

#### 多语言切换问题
- **解决方案**:
  1. 确保语言文件存在
  2. 清除缓存：
  ```bash
  php artisan cache:clear
  php artisan view:clear
  ```
  3. 检查浏览器语言设置
  4. 确保 Session 配置正确

#### 数据库连接错误
- **解决方案**:
  1. 检查数据库配置
  2. 确保 MySQL 服务运行
  3. 验证数据库用户权限
  4. 检查数据库端口是否正确

### 3. 性能优化建议

#### 生产环境优化
```bash
# 优化自动加载
composer install --optimize-autoloader --no-dev

# 缓存配置
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 清理旧日志和临时文件
php artisan log:clear
```

#### 文件上传优化
- 配置 nginx.conf：
```nginx
client_max_body_size 1024m;
```

### 4. 安全建议

1. 修改默认管理员密码
2. 启用 HTTPS
3. 定期备份数据
4. 设置合适的文件权限
5. 及时更新依赖包
6. 配置防火墙规则
7. 启用登录日志记录

## 技术支持

如需帮助，可以：
1. 查看在线文档
2. 提交 Issue
3. 发送邮件至：support@example.com

## 更新日志

### v2.0.0 (2024-03)
- 升级到 Laravel 11.x
- 重构管理后台界面
- 优化视频处理流程
- 增强系统安全性
- 改进多语言支持
- 新增数据统计功能
- 修复已知问题

## 许可证

本项目基于 MIT 许可证开源。