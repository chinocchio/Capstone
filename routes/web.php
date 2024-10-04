<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MacController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ScansController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PlotController;
use App\Exports\LogsExport;

use App\Exports\ScansExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/export/scans/excel', function () {
    return Excel::download(new ScansExport, 'scans.xlsx');
})->name('scans.export.excel');

Route::redirect('/', 'posts');

// Posts Routes
Route::resource('posts', PostController::class);

// User Posts Route 
Route::get('/{user}/posts', [DashboardController::class, 'userPosts'])->name('posts.user');


// Routes for authenticated users
Route::middleware('auth')->group(function() {

    Route::get('/user/change-pin', [UserController::class, 'showChangePinForm'])->name('user.showChangePinForm');
    Route::put('/user/change-pin', [UserController::class, 'changePinUser'])->name('user.changePin');

    Route::get('/mac-lab', [PlotController::class, 'show'])->name('mac.plot');

    Route::get('/biometrics/getFinger', [ScansController::class, 'runCSharpApp'])->name('biometrics.runApp');

    Route::get('/calendar/user', [DashboardController::class, 'showUserCalendar'])->name('subjects.userCalendar');

    Route::get('/scans/export-pdf', [DashboardController::class, 'exportPdf'])->name('scans.export.pdf');

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
    Route::post('/import-students', [DashboardController::class, 'importStudents'])->name('import.students');
    Route::get('/check-students', [DashboardController::class, 'checkStudents'])->name('check.students');
    Route::delete('/students/{id}/unenroll', [DashboardController::class, 'unenroll'])->name('students.unenroll');

    // User add subjects Route
    Route::get('/subjects', [DashboardController::class, 'toSubjects'])->name('subjects');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


// Routes for admin Dashboard
Route::middleware('admin')->prefix('admin')->group(function() 
{
    Route::get('/export/logs', function () {
        return Excel::download(new LogsExport, 'logs.xlsx');
    })->name('export.logs');

    Route::post('/set-semester-year', [AdminController::class, 'setSemesterAndYear'])->name('setSemesterAndYear');

    Route::get('/admin/change-pin', [AdminController::class, 'showChangePinForm'])->name('admin.showChangePinForm');

    // Route to handle the PIN change submission for admin
    Route::put('/admin/change-pin', [AdminController::class, 'changePin'])->name('admin.changePin');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/confirm/{id}', [ReportController::class, 'confirm'])->name('reports.confirm');
    Route::get('/reports/print/{id}', [ReportController::class, 'printPdf'])->name('reports.print');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');

    Route::get('instructors/create', [UserController::class,'create'])->name('add_instructors');
    Route::post('instructors/create', [UserController::class,'store'])->name('store_instructors');

    Route::get('/data-view', [LogsController::class, 'dataRecords'])->name('dataViewer');

    Route::post('/users/import', [UserController::class,'import'])->name("importUsersFromExcel");
    Route::get('instructors', [UserController::class, 'userShow'])->name('user.show');
    Route::delete('admin/instructors/deleteSelected', [UserController::class, 'deleteSelected'])->name('instructors.deleteSelected');

    Route::get('/calendar', [SubjectController::class, 'showCalendar'])->name('subjects.calendar');

    Route::get('/subjects/{id}/makeup-class/select', [SubjectController::class, 'selectMakeupClassTime'])->name('makeupClass');
    Route::post('/subjects/{id}/makeup-class/store', [SubjectController::class, 'storeMakeupClass'])->name('makeupClass.store');

    // Subject Routes
    Route::resource('subjects', SubjectController::class);
    Route::post('/subjects/import', [SubjectController::class,'import'])->name("importSubsFromExcel");
    Route::delete('admin/subjects/delete-all', [SubjectController::class, 'deleteAll'])->name('subjects.deleteAll');


    // MAC Routes
    Route::resource('mac',MacController::class);
    Route::post('/macs/import', [MacController::class,'import'])->name("importMacsFromExcel");
    Route::post('/mac/{mac}/unlink/{student}', [MacController::class, 'unlinkStudent'])->name('mac.unlink');

    // User Routes
    Route::resource('users', UserController::class);
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');

    // Student Route
    Route::get('/student',[StudentController::class, 'index'])->name('student_view');
    Route::post('/student/importExcel', [StudentController::class,'import'])->name("importStudentsFromExcel");
    Route::get('/students/download-duplicates', [StudentController::class, 'downloadDuplicates'])->name('students.download.duplicates');
    Route::view('/student/createStudent','admin.admins.createStudent')->name('create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
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


