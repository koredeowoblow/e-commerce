<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('user.home');
});


Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'create']);

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'index')->name('user.register');
    Route::post('/register', 'store')->name('user.store');

    Route::get('/vendor/register', 'index')->name('vendor.register');
    Route::post('/vendor/register', 'store')->name('vendor.store');

    Route::get('/admin/register', 'index')->name('admin.register');
    Route::post('/register/admin', 'registerAdmin')->name('admin.store');

    Route::get('/fetch-country', 'fetch_country')->name('fetch.country');
    Route::get('/fetch-state/{id}', 'fetch_state')->name('fetch.state');

    Route::post('/verify-email', 'verifyEmail')->name('email.verify');
});
