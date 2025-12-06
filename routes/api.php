<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\LanguageController;

Route::controller(AuthController::class)->prefix('auth/')->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::post('register', 'register');
    Route::post('verify-register-code', 'verifyOtpAndRegister');
    Route::post('resend-register-code', 'resendRegisterCode');
    Route::post('refresh', 'refresh')->middleware('auth:sanctum');
    Route::get('profile', 'userProfile')->middleware('auth:sanctum');
    Route::post('profile', 'updateProfile')->middleware('auth:sanctum');
    Route::delete('delete-account', 'deleteAccount')->middleware('auth:sanctum');
});

Route::controller(PasswordResetController::class)->prefix('auth/')->group(function (){
    Route::post('reset-password', 'resetPassword');
    Route::post('confirm-otp', 'verifyResetCode');
    Route::post('resend-otp', 'resendCodeForReset');
    Route::post('send-otp', 'sendResetCode');
});

Route::post('user/language', [LanguageController::class, 'update'])->middleware('auth:sanctum');
