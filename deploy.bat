@echo off
chcp 65001 >nul

echo 🚀 开始部署腾讯云 COS 集成版本...

:: 项目路径
set PROJECT_PATH=D:\phpstudy_pro\WWW\video-manager
set BACKUP_PATH=D:\phpstudy_pro\WWW\backups\video-manager-%date:~0,4%%date:~5,2%%date:~8,2%_%time:~0,2%%time:~3,2%%time:~6,2%

:: 创建备份
echo 📦 创建备份...
if not exist "D:\phpstudy_pro\WWW\backups" mkdir "D:\phpstudy_pro\WWW\backups"
xcopy "%PROJECT_PATH%" "%BACKUP_PATH%" /E /I /H /Y

:: 进入项目目录
cd /d "%PROJECT_PATH%"

:: 获取最新代码
echo 📥 获取最新代码...
git fetch origin
git checkout clean-cos-integration-v2
git pull origin clean-cos-integration-v2

:: 安装/更新依赖
echo 📦 安装依赖...
composer install --no-dev --optimize-autoloader

:: 清理缓存
echo 🧹 清理缓存...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

:: 运行数据库迁移
echo 🗄️ 运行数据库迁移...
php artisan migrate --force

:: 重启队列处理器
echo 🔄 重启队列处理器...
php artisan queue:restart

:: 检查服务状态
echo ✅ 检查服务状态...
php artisan videos:check-status

echo 🎉 部署完成！
echo 📝 请检查以下配置：
echo    1. .env 文件中的数据库配置
echo    2. 腾讯云 COS 配置
echo    3. 队列处理器是否正常运行
echo    4. 文件权限是否正确

pause 