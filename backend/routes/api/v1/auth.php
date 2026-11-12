<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\ReaderAuthController;
use App\Http\Controllers\Api\V1\Auth\WriterAuthController;
use App\Http\Controllers\Api\V1\Auth\AdminAuthController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

// Reader authentication
Route::prefix('auth/reader')->name('auth.reader.')->group(function () {
    Route::post('register', [ReaderAuthController::class, 'register'])->name('register');
    Route::post('login', [ReaderAuthController::class, 'login'])->name('login');
    Route::post('logout', [ReaderAuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum,reader');
    Route::post('password/email', [ReaderAuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::post('password/reset', [ReaderAuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('email/verify', [ReaderAuthController::class, 'verifyEmail'])->name('email.verify');
    Route::post('mfa/enable', [ReaderAuthController::class, 'enableMfa'])->name('mfa.enable')->middleware('auth:sanctum,reader');
    Route::post('mfa/verify', [ReaderAuthController::class, 'verifyMfa'])->name('mfa.verify')->middleware('auth:sanctum,reader');
});

// Writer authentication
Route::prefix('auth/writer')->name('auth.writer.')->group(function () {
    Route::post('register', [WriterAuthController::class, 'register'])->name('register');
    Route::post('login', [WriterAuthController::class, 'login'])->name('login');
    Route::post('logout', [WriterAuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum,writer');
    Route::post('password/email', [WriterAuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::post('password/reset', [WriterAuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('email/verify', [WriterAuthController::class, 'verifyEmail'])->name('email.verify');
});

// Administrator authentication (invitation-only creation handled in admin routes)
Route::prefix('auth/admin')->name('auth.admin.')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum,admin');
    Route::post('password/email', [AdminAuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::post('password/reset', [AdminAuthController::class, 'resetPassword'])->name('password.reset');
});
