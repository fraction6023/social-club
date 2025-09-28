<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SC_Controller;
use App\Http\Controllers\AdminUserController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');

// Breeze auth
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Protected routes (auth + verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |----------------------------------------------------------------------
    | Attendance scan (in/out)
    | - ملاحظة: dashboard.blade.php يستخدم route('attendance.scan', ['action'=>'in'|'out'])
    |----------------------------------------------------------------------
    */
    Route::get('/scan/{action}', [SC_Controller::class, 'scanPage'])
        ->where('action', 'in|out')
        ->name('attendance.scan');

    Route::post('/attendance/scan/{action}', [SC_Controller::class, 'setByScan'])
        ->where('action', 'in|out')
        ->name('attendance.set');

    /*
    |----------------------------------------------------------------------
    | Admin area (requires can:manage-users)
    | أسماء الراوت: admin.users.index / admin.users.edit / admin.users.update
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('can:manage-users')
        ->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        });
});

Route::get('/attendance/accept', [SC_Controller::class, 'acceptBySignedQr'])
    ->name('attendance.accept')
    ->middleware('signed');
