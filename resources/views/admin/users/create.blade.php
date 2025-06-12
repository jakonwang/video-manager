@extends('admin.layouts.app')

@section('title', __('admin.create_user'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-effect rounded-xl p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.create_user') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.create_user_description') }}</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 名称 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-user mr-1.5 text-primary-500 text-xs"></i>
                    {{ __('admin.name') }}
                </label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm @error('name') ring-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 @enderror">
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 邮箱 -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-envelope mr-1.5 text-primary-500 text-xs"></i>
                    {{ __('admin.email') }}
                </label>
                <div class="mt-1">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm @error('email') ring-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 @enderror">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 密码 -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-lock mr-1.5 text-primary-500 text-xs"></i>
                    {{ __('admin.password') }}
                </label>
                <div class="mt-1">
                    <input type="password" name="password" id="password" required
                           class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm @error('password') ring-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 @enderror">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 确认密码 -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-lock mr-1.5 text-primary-500 text-xs"></i>
                    {{ __('admin.confirm_password') }}
                </label>
                <div class="mt-1">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm">
                </div>
            </div>

            <!-- 角色 -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-user-shield mr-1.5 text-primary-500 text-xs"></i>
                    {{ __('admin.role') }}
                </label>
                <div class="mt-1">
                    <select name="role" id="role" required
                            class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800/50 transition-all duration-200 text-sm @error('role') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                        <option value="editor" {{ old('role') === 'editor' ? 'selected' : '' }}>{{ __('admin.role_editor') }}</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('admin.role_admin') }}</option>
                    </select>
                </div>
                @error('role')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 状态 -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-0 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800/50">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        {{ __('admin.is_active') }}
                    </label>
                </div>
                @error('is_active')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- 按钮 -->
            <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
                    {{ __('admin.back_to_list') }}
                </a>

                <button type="submit" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-primary-600 to-primary-700 border border-transparent rounded-lg text-sm font-medium text-white shadow-md hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                    <i class="fas fa-save mr-1.5 text-xs"></i>
                    {{ __('admin.create_user') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 