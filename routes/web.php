<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Mobile\VideoViewController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\VideoCategoryController;
use App\Http\Controllers\Admin\VideoDownloadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 默认首页重定向到登录页面
Route::get('/', function () {
    return redirect()->route('login');
});

// 认证路由
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');
});
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// 移动端视频查看路由
Route::prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/category/{categoryId}/video', [VideoViewController::class, 'showCategoryVideo'])->name('category.video');
    Route::get('/video/{videoId}/preview', [VideoViewController::class, 'preview'])->name('video.preview');
    Route::get('/video/{videoId}/download', [VideoViewController::class, 'download'])->name('video.download');
    Route::get('/category/{categoryId}/info', [VideoViewController::class, 'getCategoryInfo'])->name('category.info');
    Route::get('/category/{categoryId}/wait', [VideoViewController::class, 'wait'])->name('category.wait');
});

// 语言切换路由
Route::get('/language/switch/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where('locale', 'en|zh|vi');

// 后台管理路由
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 后台首页和仪表盘
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // 视频管理
    Route::resource('videos', VideoController::class);
    Route::post('videos/batch-update-usage', [VideoController::class, 'batchUpdateUsage'])->name('videos.batch-update-usage');
    Route::post('videos/batch-delete', [VideoController::class, 'batchDelete'])->name('videos.batch-delete');
    Route::get('videos/{video}/download', [VideoController::class, 'download'])->name('videos.download');
    Route::get('videos/{video}/preview', [VideoController::class, 'preview'])->name('videos.preview');
    
    // 视频分类管理
    Route::resource('video-categories', VideoCategoryController::class);
    Route::get('video-categories/{videoCategory}/videos', [VideoCategoryController::class, 'videos'])->name('video-categories.videos');

    // 下载记录管理
    Route::get('downloads', [VideoDownloadController::class, 'index'])->name('downloads.index');
    Route::get('downloads/{download}', [VideoDownloadController::class, 'show'])->name('downloads.show');
    Route::delete('downloads/{download}', [VideoDownloadController::class, 'destroy'])->name('downloads.destroy');
    Route::post('downloads/clear', [VideoDownloadController::class, 'clear'])->name('downloads.clear');

    // 用户管理
    Route::resource('users', UserController::class);

    // 系统设置
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-cos', [SettingController::class, 'testCosConnection'])->name('settings.test-cos');
});