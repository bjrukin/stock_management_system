<?php

use App\Http\Controllers\Site\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('check-login', [AuthController::class, 'checkLogin'])->name('checkLogin');
Route::get('register/{type}', [AuthController::class, 'register'])->name('register');
Route::post('register/save', [AuthController::class, 'registerSave'])->name('register.save');
Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');
Route::post('otp', [AuthController::class, 'otp'])->name('otp');
Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
Route::post('save-password', [AuthController::class, 'savePassword'])->name('save.password');