<?php

use App\Http\Controllers\Api\ApiSubjectController;
use App\Http\Controllers\Api\ApiInstructorsController;
use App\Http\Controllers\Api\ApiLinkedSubjectsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ScansController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PinVerificationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\MacController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\TemperatureController;

//for logs
Route::apiResource('logs', LogsController::class)->only([
    'store', 'index'
]);

Route::apiResource('subs', ApiSubjectController::class);
Route::get('subs/{day}', [ApiSubjectController::class, 'getScheduleByDay']);// wag to bobo to 

Route::get('subjects/{day}', [SubjectController::class, 'getScheduleByDay']);

Route::get('subjects/{pin}/{day}', [SubjectController::class, 'getScheduleByPinAndDate']);//wip

//api for instructors
Route::apiResource('instructors', ApiInstructorsController::class);

//This api is for registration of the fingerprints of the instructors
Route::put('/instructors/{email}', [ApiInstructorsController::class, 'update']);

//this api is for verifying the recorded biometric
Route::get('/instructors/pin/{pin}', [ApiInstructorsController::class, 'getByPin']);

////this api is for verifying the recorded biometric but with ALL linked subjects
Route::get('/instructors/{pin}', [ApiInstructorsController::class, 'show']);

//this api is for verifying the recorded biometric but with linked subjects
Route::get('/instructors/pin/{pin}/{day}', [ApiInstructorsController::class, 'getByPinWithSubjects']);

//kay josh to putang ina non
Route::apiResource('linkedSubjects', ApiLinkedSubjectsController::class);
Route::delete('/linkedSubjects', [ApiLinkedSubjectsController::class, 'delete']);

//getting the instructors and linked subjects
Route::get('/user/{id}/subjects', [UserController::class, 'getUserSubjects']);

Route::get('/user', function (Request $request) {
    return $request->user();    
})->middleware('auth:sanctum');

// Route for PIN authentication
Route::post('/verify-pin', [PinVerificationController::class, 'verifyPin']);
Route::post('/verify-pinDoor', [PinVerificationController::class, 'verifyPinForDoor']);

//Student login API route
Route::post('/student', [StudentController::class, 'verifyStudent']);
Route::get('/students', [StudentController::class, 'getAllStudent']);
Route::post('/students', [StudentController::class, 'storeStudent']);
Route::get('students/find-by-biometric-data', [StudentController::class, 'findByBiometricData']);

//Scan API Route
Route::post('/record-scan', [ScansController::class, 'recordScan']);
Route::post('/macs', [ScansController::class, 'linkToStudent']);
Route::get('/scans', [ScansController::class, 'getScans']);

//time
Route::get('/time/12-hour', [ScansController::class, 'get12HourFormat']);
Route::get('/time/24-hour', [ScansController::class, 'get24HourFormat']);

//fingerprint
Route::post('/register-biometrics', [StudentController::class, 'registerBiometrics']);
Route::get('fingerprints', [FingerprintController::class, 'index']);
Route::post('fingerprints', [FingerprintController::class, 'store']);
Route::apiResource('temperatures', TemperatureController::class);


//mac fo testing
// In routes/web.php or routes/api.php
// Route::get('/mac/{id}/students', [MacController::class, 'getStudents'])->name('mac.getStudents');
Route::get('/mac/{id}/students', [MacController::class, 'getStudents']);


