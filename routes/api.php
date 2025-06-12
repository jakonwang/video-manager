<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // 获取随机视频
    Route::get('/videos/random', [VideoController::class, 'show']);
    
    // 下载视频
    Route::get('/videos/{video}/download', [VideoController::class, 'download'])
        ->middleware('throttle:1,600'); // 每10分钟允许1次请求
    
    // 上传视频
    Route::post('/videos', [VideoController::class, 'store']);
});