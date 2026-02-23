<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AssignmentController;

Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});

Route::middleware('auth:sanctum')->prefix('assignments')->middleware('throttle:60,1')->group(function () {
    Route::get('/', [AssignmentController::class, 'index']);
    Route::post('/', [AssignmentController::class, 'store']);
    Route::get('{id}', [AssignmentController::class, 'show']);
    Route::put('{id}', [AssignmentController::class, 'update']);
    Route::delete('{id}', [AssignmentController::class, 'destroy']);
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Campus Assignment Management API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
})->middleware('throttle:100,1');
