<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\PasswordResetController;


Route::get('/', function () {
    return view('welcome');
});


// CSRF bootstrap
Route::get('/sanctum/csrf-cookie', fn() => response()->noContent());


Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.update');