<?php 
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\ChamberController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentPrescriptionController;
use App\Http\Controllers\Api\MedicineCompanyController;
use App\Http\Controllers\Api\MedicineController;
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

Route::get('doctors/public', [DoctorController::class, 'publicIndex']);
Route::get('doctors/public/{id}', [DoctorController::class, 'publicShow']);
Route::get('medicine-companies/public', [MedicineCompanyController::class, 'publicIndex']);
Route::get('medicine-companies/public/{id}', [MedicineCompanyController::class, 'publicShow']);
Route::get('medicines/public', [MedicineController::class, 'publicIndex']);
Route::get('medicines/public/{id}', [MedicineController::class, 'publicShow']);

Route::middleware('auth:sanctum')->group(function () {
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
});