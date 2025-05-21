<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Profile\PasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::post('/verify',[AuthController::class,'verifyUserEmail']);
Route::post('/resend-verification-email',[AuthController::class,'resendVerificationLink']);

Route::post('/reset-password',[PasswordController::class,'resetPassword'])->middleware('auth');
