<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\InvalidStateException;

#[Group('Auth')]
class GoogleLoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'access_token' => ['required', 'string'],
        ]);

        try {
            /** @var AbstractProvider $googleProvider */
            $googleProvider = Socialite::driver('google');
            $googleUser = $googleProvider
                ->userFromToken($validated['access_token']);
        } catch (InvalidStateException) {
            return response()->json(['message' => 'Invalid Google token'], 401);
        } catch (\Throwable) {
            return response()->json(['message' => 'Unable to authenticate with Google'], 401);
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Str::random(40),
                'type' => 'USER',
            ]);
        } else {
            if ($user->google_id && $user->google_id !== $googleUser->getId()) {
                return response()->json(['message' => 'Google account is already linked to another account'], 409);
            }

            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        }

        $user->refresh();

        if (! $user->is_active) {
            return response()->json(['message' => 'User inactive'], 403);
        }

        return response()->json([
            'message' => 'Google login successful',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user->fresh(),
        ]);
    }
}
