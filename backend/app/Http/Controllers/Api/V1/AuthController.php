<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\StaffProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'] ?? 'staff',
                'status' => 'pending',
            ]);

            // Create staff profile if role is staff
            if ($user->isStaff()) {
                StaffProfile::create(['user_id' => $user->id]);
            }

            return $user;
        });

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => '登録が完了しました',
            'user' => $this->formatUser($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません'],
            ]);
        }

        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'email' => ['このアカウントは停止されています'],
            ]);
        }

        $deviceName = $validated['device_name'] ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'ログインしました',
            'user' => $this->formatUser($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'ログアウトしました',
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStaff()) {
            $user->load('staffProfile');
        }

        return response()->json([
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['現在のパスワードが正しくありません'],
            ]);
        }

        $user->update(['password' => $validated['password']]);

        return response()->json([
            'message' => 'パスワードを更新しました',
        ]);
    }

    /**
     * Format user for response
     */
    private function formatUser(User $user): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
        ];

        if ($user->relationLoaded('staffProfile') && $user->staffProfile) {
            $data['staff_profile'] = [
                'phone' => $user->staffProfile->phone,
                'date_of_birth' => $user->staffProfile->date_of_birth,
                'gender' => $user->staffProfile->gender,
                'prefecture' => $user->staffProfile->prefecture,
                'city' => $user->staffProfile->city,
                'profile_photo_path' => $user->staffProfile->profile_photo_path,
                'id_verified_at' => $user->staffProfile->id_verified_at,
            ];
        }

        return $data;
    }
}
