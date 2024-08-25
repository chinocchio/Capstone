<?php

use App\Http\Controllers\Api\ApiSubjectController;
use App\Http\Controllers\Api\ApiInstructorsController;
use App\Http\Controllers\Api\ApiLinkedSubjectsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScansController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PinVerificationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\MacController;

Route::apiResource('subs', ApiSubjectController::class);
Route::apiResource('instructors', ApiInstructorsController::class);
Route::apiResource('linkedSubjects', ApiLinkedSubjectsController::class);
Route::get('/user/{id}/subjects', [UserController::class, 'getUserSubjects']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route for PIN authentication
Route::post('/verify-pin', [PinVerificationController::class, 'verifyPin']);

//Student login API route
Route::post('/student', [StudentController::class, 'verifyStudent']);
Route::get('/students', [StudentController::class, 'getAllStudent']);
Route::post('/students', [StudentController::class, 'storeStudent']);
Route::get('students/find-by-biometric-data', [StudentController::class, 'findByBiometricData']);

//Scan API Route
Route::post('/record-scan', [ScansController::class, 'recordScan']);
Route::post('/macs', [ScansController::class, 'linkToStudent']);

//fingerprint
Route::post('/register-biometrics', [StudentController::class, 'registerBiometrics']);
Route::get('fingerprints', [FingerprintController::class, 'index']);
Route::post('fingerprints', [FingerprintController::class, 'store']);

