<?php

use App\Http\Controllers\Api\AmbulanceController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentPrescriptionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\BloodDonorController;
use App\Http\Controllers\Api\ChamberController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\MedicineCompanyController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfessionalExperienceController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\StoreProductController;
use App\Http\Controllers\Api\StoreStockController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->patch('profile', [AuthController::class, 'updateProfile']);
Route::post('register', [AuthController::class, 'register']);

Route::prefix('auth')->group(function () {
    Route::post('/otp-send', [ForgotPasswordController::class, 'sendOtp']);
    Route::post('/otp-verify', [ForgotPasswordController::class, 'verifyOtp']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

Route::get('doctors/public', [DoctorController::class, 'publicIndex']);
Route::get('doctors/public/{id}', [DoctorController::class, 'publicShow']);
Route::get('medicine-companies/public', [MedicineCompanyController::class, 'publicIndex']);
Route::get('medicine-companies/public/{id}', [MedicineCompanyController::class, 'publicShow']);
Route::get('medicines/public', [MedicineController::class, 'publicIndex']);
Route::get('medicines/public/{id}', [MedicineController::class, 'publicShow']);

// Public posts (read-only)
Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{id}', [PostController::class, 'show']);

Route::get('blood-donors/public', [BloodDonorController::class, 'publicIndex']);
Route::get('blood-donors/public/{id}', [BloodDonorController::class, 'publicShow']);

Route::get('ambulances/public', [AmbulanceController::class, 'publicIndex']);
Route::get('ambulances/public/{id}', [AmbulanceController::class, 'publicShow']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('posts', [PostController::class, 'store']);
    Route::post('posts/{post}/comments', [PostController::class, 'comment']);
    Route::post('posts/{post}/ratings', [PostController::class, 'rate']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('educations', EducationController::class);
    Route::apiResource('professional-experiences', ProfessionalExperienceController::class);
    Route::apiResource('chambers', ChamberController::class);
    Route::apiResource('doctor-schedules', DoctorScheduleController::class);
    Route::apiResource('hospitals', HospitalController::class);
    Route::apiResource('medicine-companies', MedicineCompanyController::class);
    Route::apiResource('medicines', MedicineController::class);
    Route::get('appointments/upcoming', [AppointmentController::class, 'upcoming']);
    Route::get('my-appointments', [AppointmentController::class, 'myAppointments']);
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('my-prescriptions', [AppointmentPrescriptionController::class, 'myPrescriptions']);
    Route::apiResource('appointment-prescriptions', AppointmentPrescriptionController::class);
    Route::get('users/phone/{phone}', [UserController::class, 'findByPhone']);
    Route::post('users/phone/{phone}', [UserController::class, 'findOrCreateByPhone']);
    Route::patch('blood-donors/interest', [BloodDonorController::class, 'updateInterest']);
    Route::get('blood-donors', [BloodDonorController::class, 'index']);
    Route::post('blood-donations', [BloodDonorController::class, 'store']);
    Route::get('my-blood-donations', [BloodDonorController::class, 'myDonations']);
    Route::get('ambulances', [AmbulanceController::class, 'index']);
    Route::post('ambulances', [AmbulanceController::class, 'store']);
    Route::get('ambulances/{id}', [AmbulanceController::class, 'show']);
    Route::put('ambulances/{id}', [AmbulanceController::class, 'update']);
    Route::delete('ambulances/{id}', [AmbulanceController::class, 'destroy']);

    Route::apiResource('stores', StoreController::class);
    Route::post('stores/{storeId}/orders', [OrderController::class, 'store']);
    Route::get('my-orders', [OrderController::class, 'myOrders']);
    Route::get('my-orders/{orderId}', [OrderController::class, 'showMine']);

    Route::prefix('stores/{storeId}')->group(function () {
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{orderId}', [OrderController::class, 'show']);
        Route::patch('orders/{orderId}/status', [OrderController::class, 'updateStatus']);
        Route::apiResource('products', StoreProductController::class);

        Route::prefix('stocks')->group(function () {
            Route::get('/', [StoreStockController::class, 'index']);
            Route::post('/', [StoreStockController::class, 'store']);
            Route::get('/summary', [StoreStockController::class, 'getStockSummary']);
            Route::get('/{stockId}', [StoreStockController::class, 'show']);
        });

        Route::get('/products/{productId}/stocks', [StoreStockController::class, 'getProductStock']);
    });
});
