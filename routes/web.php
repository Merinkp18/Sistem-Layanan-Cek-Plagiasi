<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::redirect('/', '/login');

Route::middleware('guest.actor')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/registrasi', [AuthController::class, 'showRegistration'])->name('registration.create');
    Route::post('/registrasi', [AuthController::class, 'register'])->name('registration.store');
});

Route::middleware('auth.actor')->group(function (): void {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
