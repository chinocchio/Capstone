<?php

use App\Http\Controllers\Api\ApiSubjectController;
use App\Http\Controllers\Api\ApiInstructorsController;
use App\Http\Controllers\Api\ApiLinkedSubjectsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('subs', ApiSubjectController::class);
Route::apiResource('instructors', ApiInstructorsController::class);
Route::apiResource('linkedSubjects', ApiLinkedSubjectsController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
