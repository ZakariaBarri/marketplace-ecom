<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => new AuthResource($user),
            'token' => $token
        ], 'User registered successfully');
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return $this->error('Invalid credentials', null, 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => new AuthResource($user),
            'token' => $token
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    //هذه الدالة تُعيد بيانات المستخدم الذي أرسل الـ Token.
    //مفيدة للواجهة الأمامية لعرض بيانات الحساب.
    public function me()
    {
        $user = auth()->user();

        return $this->success(
            new AuthResource($user),
            'Current user data'
        );
    }

    //هذه الدالة تحذف كل التوكنات الخاصة بالمستخدم.
    //تستخدم عندما يريد المستخدم تسجيل الخروج من كل الأجهزة.
    public function logoutAll()
    {
        auth()->user()->tokens()->delete();

        return $this->success(
            null,
            'Logged out from all devices'
        );
    }
}
