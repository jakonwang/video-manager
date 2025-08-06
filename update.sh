#!/bin/bash

# 视频管理系统快速更新脚本
# 适用于现有版本的快速更新

set -e

echo "🚀 开始更新视频管理系统..."

# 颜色定义
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 备份配置文件
echo -e "${GREEN}[INFO]${NC} 备份配置文件..."
cp .env .env.backup 2>/dev/null || echo -e "${YELLOW}[WARN]${NC} .env 文件不存在"
cp storage/app/settings.json storage/app/settings.json.backup 2>/dev/null || echo -e "${YELLOW}[WARN]${NC} settings.json 文件不存在"

# 获取最新代码
echo -e "${GREEN}[INFO]${NC} 获取最新代码..."
git fetch origin
git reset --hard origin/main
git clean -fd

# 恢复配置文件
echo -e "${GREEN}[INFO]${NC} 恢复配置文件..."
cp .env.backup .env 2>/dev/null || echo -e "${YELLOW}[WARN]${NC} 无法恢复 .env 文件"
cp storage/app/settings.json.backup storage/app/settings.json 2>/dev/null || echo -e "${YELLOW}[WARN]${NC} 无法恢复 settings.json 文件"

# 更新依赖
echo -e "${GREEN}[INFO]${NC} 更新依赖..."
composer install --no-dev --optimize-autoloader

# 清理缓存
echo -e "${GREEN}[INFO]${NC} 清理缓存..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 优化生产环境
echo -e "${GREEN}[INFO]${NC} 优化生产环境..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 重启队列
echo -e "${GREEN}[INFO]${NC} 重启队列处理器..."
php artisan queue:restart

echo -e "${GREEN}[SUCCESS]${NC} 更新完成！"
echo ""
echo "📋 更新内容："
echo "   ✅ 腾讯云 COS 存储集成"
echo "   ✅ 管理后台配置界面"
echo "   ✅ 部署脚本和文档"
echo "   ✅ 性能优化"
echo ""
echo "🔧 后续步骤："
echo "   1. 检查网站是否正常访问"
echo "   2. 测试文件上传功能"
echo "   3. 配置腾讯云 COS（如需要）"
echo "" 