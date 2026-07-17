<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Persistence\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * OTP Generation
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => ['required'] // email or phone
        ]);

        $identifier = $request->identifier;

        // 🔹 Check user exists
        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // 🔹 Generate OTP
        $otp = rand(100000, 999999);

        // 🔹 Save / update OTP
        DB::table('otps')->updateOrInsert(
            ['identifier' => $identifier],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
                'updated_at' => now()
            ]
        );

        // NEXT UPDATE: sendSMS or SenD Email

        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp // ⚠️ testing purpose only
        ]);
    }

    /**
     * OTP Verification
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'identifier' => ['required'],
            'otp' => ['required']
        ]);

        $record = DB::table('otps')
            ->where('identifier', $request->identifier)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        if (now()->gt($record->expires_at)) {
            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        return response()->json([
            'message' => 'OTP verified'
        ]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'identifier' => ['required'],
            'password' => ['required', 'min:6']
        ]);

        $user = User::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // 🔥 Optional: check OTP verified আগে (flag add করতে পারো)

        $user->update([
            'password' => $request->password // mutator hash করবে
        ]);

        // 🔹 OTP delete করে দাও
        DB::table('otps')
            ->where('identifier', $request->identifier)
            ->delete();

        return response()->json([
            'message' => 'Password reset successful'
        ]);
    }
}
