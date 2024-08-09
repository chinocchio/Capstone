<?php

use App\Http\Controllers\Api\ApiSubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('subs', ApiSubjectController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
