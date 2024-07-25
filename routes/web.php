<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'posts');

// Posts Routes
Route::resource('posts', PostController::class);

// User Posts Route
Route::get('/{user}/posts', [DashboardController::class, 'userPosts'])->name('posts.user');


// Routes for authenticated users
Route::middleware('auth')->group(function() {

    // User Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User attendace Route
    Route::get('/attendance', [DashboardController::class, 'toAttendance'])->name('attendance');

    // User seat plan Route
    Route::get('/seatplan', [DashboardController::class, 'toSeatplan'])->name('seatplan');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



});

// Routes for guest users
Route::middleware('guest')->group(function() {

    // Register Routes
    Route::view('/register','auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login Routes
    Route::view('/login','auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    //Google Auth Route
    Route::get('auth/google',[GoogleAuthController::class,'redirect'])->name('google-auth');
    Route::get('auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

});


