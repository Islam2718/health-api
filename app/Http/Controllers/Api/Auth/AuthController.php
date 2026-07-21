<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['identifier'])
            ->orWhere('phone', $validated['identifier'])            
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'User inactive'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Logout
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Register
     *
     * (email / phone)
     *
     * @response 201 {
     *   "message": "User registered successfully",
     *   "token": "1|abc123exampletoken",
     *   "user": {
     *     "id": 1,
     *     "name": "Rafi",
     *     "email": "rafi@mail.com"
     *   }
     * }
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json([
                'message' => 'Email or phone is required'
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'type' => 'USER',
        ]);

        // 🔹 Create token (Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * Update profile (ONLY USE TOKEN NO ID)
     *
     * @authenticated
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'unique:users,phone,' . $request->user()->id],
            'password' => ['nullable', 'string', 'min:6'],
            'type' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'profile_image' => ['nullable'],
            'address' => ['nullable', 'string'],
            'blood_group' => ['nullable', 'string', 'max:20'],
            'marital_status' => ['nullable', 'string', 'max:50'],
        ]);

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user = $request->user();
        $user->fill($validated);
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    }
}
