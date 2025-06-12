<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = AdminUser::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_users'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,editor'],
            'is_active' => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        AdminUser::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', '管理员创建成功');
    }

    public function edit(AdminUser $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, AdminUser $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_users,email,' . $user->id],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,editor'],
            'is_active' => ['boolean'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', '管理员更新成功');
    }

    public function destroy(AdminUser $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', '不能删除当前登录的用户');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', '管理员删除成功');
    }
} 