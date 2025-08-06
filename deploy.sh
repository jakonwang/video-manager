#!/bin/bash

# 视频管理系统部署脚本
# 适用于 Linux 服务器

set -e

echo "🚀 开始部署视频管理系统..."

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 项目配置
PROJECT_NAME="video-manager"
PROJECT_PATH="/var/www/video-manager"
BACKUP_PATH="/var/www/backup"
GIT_REPO="https://github.com/jakonwang/video-manager.git"

# 日志函数
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查是否为 root 用户
check_root() {
    if [[ $EUID -eq 0 ]]; then
        log_warn "检测到 root 用户，建议使用普通用户运行此脚本"
    fi
}

# 检查系统要求
check_requirements() {
    log_info "检查系统要求..."
    
    # 检查 PHP
    if ! command -v php &> /dev/null; then
        log_error "PHP 未安装，请先安装 PHP 8.2+"
        exit 1
    fi
    
    # 检查 Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer 未安装，请先安装 Composer"
        exit 1
    fi
    
    # 检查 Git
    if ! command -v git &> /dev/null; then
        log_error "Git 未安装，请先安装 Git"
        exit 1
    fi
    
    log_info "系统要求检查通过"
}

# 备份当前版本
backup_current() {
    if [ -d "$PROJECT_PATH" ]; then
        log_info "备份当前版本..."
        mkdir -p "$BACKUP_PATH"
        cp -r "$PROJECT_PATH" "$BACKUP_PATH/$(date +%Y%m%d_%H%M%S)_backup"
        log_info "备份完成"
    fi
}

# 获取最新代码
get_latest_code() {
    log_info "获取最新代码..."
    
    if [ -d "$PROJECT_PATH" ]; then
        # 如果项目已存在，更新代码
        cd "$PROJECT_PATH"
        
        # 备份配置文件
        if [ -f ".env" ]; then
            cp .env .env.backup
        fi
        if [ -f "storage/app/settings.json" ]; then
            cp storage/app/settings.json storage/app/settings.json.backup
        fi
        
        # 获取最新代码
        git fetch origin
        git reset --hard origin/main
        git clean -fd
        
        # 恢复配置文件
        if [ -f ".env.backup" ]; then
            cp .env.backup .env
        fi
        if [ -f "storage/app/settings.json.backup" ]; then
            cp storage/app/settings.json.backup storage/app/settings.json
        fi
        
    else
        # 如果项目不存在，克隆新项目
        cd /var/www
        git clone "$GIT_REPO" "$PROJECT_NAME"
        cd "$PROJECT_PATH"
    fi
    
    log_info "代码获取完成"
}

# 安装依赖
install_dependencies() {
    log_info "安装 PHP 依赖..."
    composer install --no-dev --optimize-autoloader
    
    log_info "安装前端依赖..."
    npm install
    npm run build
    
    log_info "依赖安装完成"
}

# 配置环境
setup_environment() {
    log_info "配置环境..."
    
    # 复制环境配置文件
    if [ ! -f ".env" ]; then
        cp .env.example .env
        log_warn "请编辑 .env 文件配置数据库和腾讯云 COS 信息"
    fi
    
    # 生成应用密钥
    php artisan key:generate
    
    # 创建存储链接
    php artisan storage:link
    
    log_info "环境配置完成"
}

# 设置权限
set_permissions() {
    log_info "设置文件权限..."
    
    # 设置目录权限
    chmod -R 755 storage/
    chmod -R 755 bootstrap/cache/
    
    # 设置所有者（根据实际情况调整）
    if command -v www-data &> /dev/null; then
        chown -R www-data:www-data storage/
        chown -R www-data:www-data bootstrap/cache/
    elif command -v nginx &> /dev/null; then
        chown -R nginx:nginx storage/
        chown -R nginx:nginx bootstrap/cache/
    else
        log_warn "未找到 web 服务器用户，请手动设置权限"
    fi
    
    log_info "权限设置完成"
}

# 数据库迁移
run_migrations() {
    log_info "运行数据库迁移..."
    
    # 检查数据库连接
    if php artisan migrate:status &> /dev/null; then
        php artisan migrate --force
        log_info "数据库迁移完成"
    else
        log_warn "数据库连接失败，请检查 .env 配置"
        log_warn "跳过数据库迁移"
    fi
}

# 清理缓存
clear_cache() {
    log_info "清理缓存..."
    
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    log_info "缓存清理完成"
}

# 优化生产环境
optimize_production() {
    log_info "优化生产环境..."
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    log_info "生产环境优化完成"
}

# 启动队列处理器
start_queue_worker() {
    log_info "启动队列处理器..."
    
    # 检查是否已有队列进程
    if pgrep -f "queue:work" > /dev/null; then
        log_info "重启队列处理器..."
        php artisan queue:restart
    else
        log_info "启动队列处理器..."
        nohup php artisan queue:work --daemon > /dev/null 2>&1 &
    fi
    
    log_info "队列处理器启动完成"
}

# 验证部署
verify_deployment() {
    log_info "验证部署..."
    
    # 检查应用状态
    if php artisan videos:check-status &> /dev/null; then
        log_info "应用状态检查通过"
    else
        log_warn "应用状态检查失败"
    fi
    
    # 检查队列状态
    if php artisan queue:failed &> /dev/null; then
        log_info "队列状态检查通过"
    else
        log_warn "队列状态检查失败"
    fi
    
    log_info "部署验证完成"
}

# 显示部署信息
show_deployment_info() {
    log_info "部署完成！"
    echo ""
    echo "📋 部署信息："
    echo "   项目路径: $PROJECT_PATH"
    echo "   备份路径: $BACKUP_PATH"
    echo "   应用 URL: http://your-domain.com"
    echo "   管理后台: http://your-domain.com/admin"
    echo ""
    echo "🔧 后续步骤："
    echo "   1. 编辑 .env 文件配置数据库和腾讯云 COS"
    echo "   2. 配置 Web 服务器（Nginx/Apache）"
    echo "   3. 设置 SSL 证书"
    echo "   4. 配置域名解析"
    echo ""
    echo "📚 相关文档："
    echo "   - REMOTE_DEPLOYMENT.md - 详细部署指南"
    echo "   - DEPLOYMENT_CHECKLIST.md - 部署检查清单"
    echo "   - COS_INTEGRATION.md - 腾讯云 COS 集成说明"
    echo ""
}

# 主函数
main() {
    echo "=========================================="
    echo "    视频管理系统部署脚本"
    echo "=========================================="
    echo ""
    
    check_root
    check_requirements
    backup_current
    get_latest_code
    install_dependencies
    setup_environment
    set_permissions
    run_migrations
    clear_cache
    optimize_production
    start_queue_worker
    verify_deployment
    show_deployment_info
    
    echo "=========================================="
    echo "    部署完成！"
    echo "=========================================="
}

# 执行主函数
main "$@" 