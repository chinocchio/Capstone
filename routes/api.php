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

//Scan API Route
Route::post('/record-scan', [ScansController::class, 'recordScan']);

//fingerprint
Route::post('/register-biometrics', [StudentController::class, 'registerBiometrics']);

