<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MacController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'posts');

// Posts Routes
Route::resource('posts', PostController::class);

// User Posts Route
Route::get('/{user}/posts', [DashboardController::class, 'userPosts'])->name('posts.user');


// Routes for authenticated users
Route::middleware('auth')->group(function() {

    //AJAX Route
    Route::get('/scans/list', [DashboardController::class, 'fetchScans'])->name('scans.list');

    Route::get('edit-subjects', [UserController::class, 'showDashboard'])->name('user.dashboard');
    Route::post('link-subject', [UserController::class, 'linkSubject'])->name('user.linkSubject');
    Route::post('unlink-subject', [UserController::class, 'unlinkSubject'])->name('user.unlinkSubject');

    // User Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User attendace Route
    Route::get('/attendance', [DashboardController::class, 'toAttendance'])->name('attendance');

    // User seat plan Route
    Route::get('/seatplan', [DashboardController::class, 'toSeatplan'])->name('seatplan');

    // User add subjects Route
    Route::get('/subjects', [DashboardController::class, 'toSubjects'])->name('subjects');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


// Routes for admin Dashboard
Route::middleware('admin')->prefix('admin')->group(function() 
{
    // Subject Routes
    Route::resource('subjects', SubjectController::class);
    Route::post('/subjects/import', [SubjectController::class,'import'])->name("importSubsFromExcel");

    // MAC Routes
    Route::resource('mac',MacController::class);
    Route::post('/macs/import', [MacController::class,'import'])->name("importMacsFromExcel");

    // User Routes
    Route::resource('users', UserController::class);
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');

    // Student Route
    Route::view('/student/import','admin.admins.addStudents')->name('studentImport');
    Route::post('/student/importExcel', [StudentController::class,'import'])->name("importStudentsFromExcel");
});

//Admin Login Routes
Route::middleware('guest')->prefix('admin')->group(function() {

    Route::get('/login',[AdminController::class, 'login']);
    Route::post('/login',[AdminController::class, 'login_submit'])->name('admin_login_submit');
    Route::post('/logout',[AdminController::class, 'logout'])->name('admin_logout');
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


