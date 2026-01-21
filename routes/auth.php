<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::post("/register", [App\Http\Controllers\V1\Auth\RegistrationController::class, 'register']);
        Route::post("/login", [App\Http\Controllers\V1\Auth\AuthSessionController::class, 'login']);
        Route::post("/forgot-password", [App\Http\Controllers\V1\Auth\RegistrationController::class, 'forgotPassword']);
        Route::post("/reset-password", [App\Http\Controllers\V1\Auth\RegistrationController::class, 'resetPassword']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post("/verify-email", [App\Http\Controllers\V1\Auth\RegistrationController::class, 'verifyEmail']);
        Route::post("/resend-otp", [App\Http\Controllers\V1\Auth\RegistrationController::class, 'resendOtp']);
        Route::post("/logout", [App\Http\Controllers\V1\Auth\AuthSessionController::class, 'logout']);
        Route::post("/refresh", [App\Http\Controllers\V1\Auth\AuthSessionController::class, 'refresh']);
        Route::get("/me", [App\Http\Controllers\V1\Auth\AuthSessionController::class, 'me']);
    });
});
