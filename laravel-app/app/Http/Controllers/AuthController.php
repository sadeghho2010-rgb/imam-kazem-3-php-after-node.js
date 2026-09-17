<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian; // Assuming Morilog Jalali is used in production

class AuthController extends Controller
{
    /**
     * Handle user login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'نام کاربری یا رمز عبور اشتباه است.'
            ], 401);
        }

        if (!$user->isActive) {
            return response()->json([
                'error' => 'حساب کاربری شما غیرفعال شده است. لطفا با مدیر سیستم تماس بگیرید.'
            ], 403);
        }

        // Generate token (using Laravel Sanctum or default Token engine)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update last login
        $user->lastLogin = now();
        $user->save();

        // Register Audit Log
        AuditLog::create([
            'userId' => $user->id,
            'userName' => $user->name,
            'roleTitle' => $user->roleTitle,
            'action' => 'ورود به سیستم',
            'module' => 'امنیت',
            'details' => ['ip' => $request->ip()],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(), // Or Jalalian equivalent
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'ورود با موفقیت انجام شد.'
        ]);
    }

    /**
     * Get the authenticated user profile.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();

            // Register Audit Log
            AuditLog::create([
                'userId' => $user->id,
                'userName' => $user->name,
                'roleTitle' => $user->roleTitle,
                'action' => 'خروج از سیستم',
                'module' => 'امنیت',
                'details' => [],
                'ip' => $request->ip(),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        return response()->json([
            'message' => 'خروج با موفقیت انجام شد.'
        ]);
    }

    /**
     * Update user credentials or permissions (Admin only).
     */
    public function updateCredentials(Request $request, $id)
    {
        $currentUser = $request->user();

        if (!$currentUser->isSuperAdmin()) {
            return response()->json([
                'error' => 'دسترسی فقط برای سوپر ادمین مجاز است.'
            ], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string',
            'role' => 'sometimes|required|string',
            'roleTitle' => 'sometimes|required|string',
            'password' => 'sometimes|nullable|string|min:4',
            'allowedTabs' => 'sometimes|array',
            'isActive' => 'sometimes|boolean',
        ]);

        $data = $request->except(['password']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Audit Log
        AuditLog::create([
            'userId' => $currentUser->id,
            'userName' => $currentUser->name,
            'roleTitle' => $currentUser->roleTitle,
            'action' => "ویرایش مشخصات کاربر: {$user->username}",
            'module' => 'مدیریت کاربران',
            'details' => ['updated_user_id' => $user->id],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'اطلاعات کاربر با موفقیت بروزرسانی شد.'
        ]);
    }
}
