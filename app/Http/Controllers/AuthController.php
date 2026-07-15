<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Application\UseCases\LoginUser;
use App\Application\UseCases\RegisterUser;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $useCase)
    {
        $user = $useCase->execute($request->validated());

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    public function login(LoginRequest $request, LoginUser $useCase)
    {
        try {
            $result = $useCase->execute(
                $request->identifier,
                $request->password
            );

            return response()->json([
                'status' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out'
        ]);
    }
}