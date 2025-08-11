<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\SupervisorController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::middleware('role:admin')->group(function () {
        Route::get('/admin',[AdminController::class, 'index']);

        Route::get('/users',[UserController::class, 'index']);

        Route::post('/users', [UserController::class, 'store']);

        Route::put('/users/{user}', [UserController::class, 'update']);

        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/interns/link', [UserController::class, 'linkIntern']);
    });


    Route::middleware('role:supervisor')->group(function () {
        Route::get('/supervisor',[SupervisorController::class, 'index']);
    });


    Route::middleware('role:intern')->group(function () {
        Route::get('/intern',[InternController::class, 'index']);
    });
});
