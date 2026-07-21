<?php 
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\ProfessionalExperienceController;
use App\Http\Controllers\Api\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->patch('/profile', [AuthController::class, 'updateProfile']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('auth')->group(function () {
    Route::post('/otp-send', [ForgotPasswordController::class, 'sendOtp']);
    Route::post('/otp-verify', [ForgotPasswordController::class, 'verifyOtp']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('educations', EducationController::class);
    Route::apiResource('professional-experiences', ProfessionalExperienceController::class);
});