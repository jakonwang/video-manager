<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * 切换语言
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $locale)
    {
        // 验证语言是否支持
        if (!in_array($locale, ['en', 'zh', 'vi'])) {
            $locale = 'en';
        }
        
        // 设置语言
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // 如果是AJAX请求，返回JSON响应
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('admin.language_switched'),
                'locale' => $locale
            ]);
        }
        
        // 如果有重定向URL，使用它
        if ($request->has('redirect')) {
            return redirect(urldecode($request->redirect));
        }
        
        // 否则返回上一页
        return redirect()->back();
    }
} 