<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('admin')->user();
        
        if (!$user->is_active) {
            Auth::guard('admin')->logout();
            return redirect()->route('login')->with('error', '您的账号已被禁用');
        }

        // 这里可以添加额外的管理员权限检查
        // if (!Auth::user()->isAdmin()) {
        //     return redirect()->route('home')->with('error', '无权访问管理后台');
        // }

        return $next($request);
    }
} 