#!/bin/bash

# 修复 Composer Lock 文件不同步问题
# 适用于线上服务器

set -e

echo "🔧 开始修复 Composer Lock 文件..."

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

# 备份当前文件
echo -e "${GREEN}[INFO]${NC} 备份当前文件..."
cp composer.json composer.json.backup
cp composer.lock composer.lock.backup 2>/dev/null || echo -e "${YELLOW}[WARN]${NC} composer.lock 文件不存在"

# 清理 Composer 缓存
echo -e "${GREEN}[INFO]${NC} 清理 Composer 缓存..."
composer clear-cache

# 删除现有的 vendor 目录和 lock 文件
echo -e "${GREEN}[INFO]${NC} 清理现有依赖..."
rm -rf vendor/
rm -f composer.lock

# 重新安装依赖（生产环境）
echo -e "${GREEN}[INFO]${NC} 重新安装依赖包..."
composer install --no-dev --optimize-autoloader --no-interaction

# 验证安装
echo -e "${GREEN}[INFO]${NC} 验证安装..."
composer validate

# 检查特定包是否安装
echo -e "${GREEN}[INFO]${NC} 检查关键包..."
if composer show qcloud/cos-sdk-v5 >/dev/null 2>&1; then
    echo -e "${GREEN}[SUCCESS]${NC} qcloud/cos-sdk-v5 包安装成功"
else
    echo -e "${RED}[ERROR]${NC} qcloud/cos-sdk-v5 包安装失败"
    exit 1
fi

# 重新生成 autoload 文件
echo -e "${GREEN}[INFO]${NC} 重新生成 autoload 文件..."
composer dump-autoload --optimize --no-dev

# 清理 Laravel 缓存
echo -e "${GREEN}[INFO]${NC} 清理 Laravel 缓存..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 清理备份文件
rm -f composer.json.backup
rm -f composer.lock.backup

echo -e "${GREEN}[SUCCESS]${NC} Composer Lock 文件修复完成！"
echo ""
echo "📋 修复内容："
echo "   ✅ 重新生成 composer.lock 文件"
echo "   ✅ 安装所有必需的依赖包"
echo "   ✅ 验证包安装状态"
echo "   ✅ 优化自动加载"
echo "   ✅ 清理所有缓存"
echo ""
echo "🔧 后续步骤："
echo "   1. 检查网站是否正常访问"
echo "   2. 测试腾讯云 COS 功能"
echo "   3. 检查错误日志"
echo "" 