<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Default Laravel welcome page
Route::get('/', function () {
    return view('welcome');
});

// React catch-all route (for SPA)
Route::get('/{any}', function () {
    return file_get_contents(public_path('react/index.html'));
})->where('any', '.*');

// Authentication routes
Auth::routes(['verify' => true]);

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Password Reset routes
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
