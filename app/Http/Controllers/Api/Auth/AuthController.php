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
            ->orWhere('username', $validated['identifier'])
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
     * (email / phone / username)
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
        // 🔹 Validate (email / phone / username - any one required)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],

            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'unique:users,phone'],
            'username' => ['nullable', 'string', 'unique:users,username'],
        ]);

        // 🔥 Ensure at least one identifier দেওয়া আছে
        if (!$request->email && !$request->phone && !$request->username) {
            return response()->json([
                'message' => 'Email or phone or username is required'
            ], 422);
        }

        // 🔹 Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'username' => $validated['username'] ?? null,
            'password' => $validated['password'],
        ]);

        // 🔹 Create token (Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }
}
