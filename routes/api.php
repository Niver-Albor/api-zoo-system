<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Módulos de Animales.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/animals', [AnimalController::class, 'index'])
    ->middleware('permission:animals.view');

    Route::get('/animals/{code}', [AnimalController::class, 'show'])
        ->middleware('permission:animals.view');

    Route::post('/animals', [AnimalController::class, 'store'])
        ->middleware('permission:animals.create');

    Route::put('/animals/{code}', [AnimalController::class, 'update'])
        ->middleware('permission:animals.update');

    Route::delete('/animals/{code}', [AnimalController::class, 'destroy'])
        ->middleware('permission:animals.delete');
});

// Módulo de Usuarios.
Route::post('/login', [UserController::class, 'login'])
    ->name('login');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create');

    Route::get('/users/who/{user}', [UserController::class, 'who'])
        ->middleware('permission:users.view_any');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->middleware('permission:users.view');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.update_own');

    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);
});