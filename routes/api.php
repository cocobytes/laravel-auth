<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailController;
use App\Http\Controllers\Auth\PasswordController;

Route::prefix('auth')->group(function() {
    // Autenticacion del usuario
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/signin', [AuthController::class, 'signin']);
    // Verificacion de correos electronicos
    Route::post('/verify-email', [EmailController::class, 'verifyEmail']);
    Route::post('/resend-verify-email', [EmailController::class, 'resendVerifyEmail']);
    // Reseteo y recuperacion de contraseñas
    Route::post('/forgot-password', [PasswordController::class, 'forgotPassword']);
    Route::post('/confirm-forgot-password', [PasswordController::class, 'confirmForgotPassword']);
});
