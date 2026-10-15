<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubmissionController;

Route::redirect('/', '/login');

Route::middleware('guest.actor')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/registrasi', [AuthController::class, 'showRegistration'])->name('registration.create');
    Route::post('/registrasi', [AuthController::class, 'register'])->name('registration.store');
});

Route::middleware('auth.actor')->group(function (): void {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengajuan', [SubmissionController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [SubmissionController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengecekan-saya', [SubmissionController::class, 'index'])->name('pengecekan-saya.index');
    Route::get('/riwayat-saya', [SubmissionController::class, 'history'])->name('riwayat-saya.index');
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile.edit');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/pengecekan-saya/{submission}', [SubmissionController::class, 'show'])
        ->whereNumber('submission')
        ->name('pengecekan-saya.show');
    Route::get('/pengecekan-saya/{submission}/file/{file}', [SubmissionController::class, 'download'])
        ->whereNumber('submission')
        ->name('pengecekan-saya.file');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
