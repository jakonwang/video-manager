#!/bin/bash

# 线上服务器 Composer 包更新脚本
# 适用于生产环境的 Composer 依赖更新

set -e

echo "🚀 开始更新 Composer 包..."

# 颜色定义
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# 检查是否在项目目录
if [ ! -f "composer.json" ]; then
    echo -e "${RED}[ERROR]${NC} 请在项目根目录运行此脚本"
    exit 1
fi

# 备份 composer.lock
echo -e "${GREEN}[INFO]${NC} 备份 composer.lock..."
cp composer.lock composer.lock.backup

# 更新 Composer 自身
echo -e "${GREEN}[INFO]${NC} 更新 Composer..."
composer self-update --no-interaction

# 清理 Composer 缓存
echo -e "${GREEN}[INFO]${NC} 清理 Composer 缓存..."
composer clear-cache

# 安装/更新依赖（生产环境优化）
echo -e "${GREEN}[INFO]${NC} 安装/更新依赖包..."
composer install --no-dev --optimize-autoloader --no-interaction

# 检查是否有新包需要安装
if [ -f "composer.lock" ] && [ -f "composer.lock.backup" ]; then
    if ! cmp -s composer.lock composer.lock.backup; then
        echo -e "${GREEN}[INFO]${NC} 检测到依赖包更新，正在优化..."
        
        # 重新生成 autoload 文件
        composer dump-autoload --optimize --no-dev
        
        # 清理 Laravel 缓存
        php artisan config:clear
        php artisan cache:clear
        php artisan view:clear
        php artisan route:clear
        
        echo -e "${GREEN}[SUCCESS]${NC} 依赖包更新完成！"
    else
        echo -e "${YELLOW}[INFO]${NC} 没有新的依赖包需要更新"
    fi
else
    echo -e "${GREEN}[INFO]${NC} 首次安装，正在优化..."
    composer dump-autoload --optimize --no-dev
fi

# 清理备份文件
rm -f composer.lock.backup

echo -e "${GREEN}[SUCCESS]${NC} Composer 包更新完成！"
echo ""
echo "📋 更新内容："
echo "   ✅ Composer 自身更新"
echo "   ✅ 依赖包安装/更新"
echo "   ✅ 自动加载优化"
echo "   ✅ Laravel 缓存清理"
echo ""
echo "🔧 后续步骤："
echo "   1. 检查网站是否正常访问"
echo "   2. 测试新功能"
echo "   3. 检查错误日志"
echo "" 