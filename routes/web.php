<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SC_Controller;
use App\Http\Controllers\AdminUserController;

Route::view('/', 'welcome');

// Breeze auth
require __DIR__ . '/auth.php';

Route::middleware(['auth','verified'])->group(function () {

    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    // Scan (in/out)
    Route::get('/scan/{action}', [SC_Controller::class, 'scanPage'])
        ->whereIn('action', ['in','out'])
        ->name('attendance.scan');

    Route::post('/attendance/scan/{action}', [SC_Controller::class, 'setByScan'])
        ->whereIn('action', ['in','out'])
        ->name('attendance.set');

    // Admin area (يتطلب مشرف)
    Route::middleware('can:manage-users')
        ->prefix('admin')->name('admin.')
        ->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        });
});
