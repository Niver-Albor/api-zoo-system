<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\UserController;
use App\Models\MedicalRecord;
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
    Route::post('/users', [UserController::class, 'store']);

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->withTrashed();

    Route::get('/users', [UserController::class, 'index']);

    Route::put('/users/{user}', [UserController::class, 'update']);

    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);
});

// Módulo de Historial Médico.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/med_records', [MedicalRecordController::class, 'store']);

    Route::put('/med_records/{medical_record}', [MedicalRecordController::class, 'createAmendment']);

    Route::get('/med_records', [MedicalRecordController::class, 'index']);

    Route::get('/med_records/{medical_record}', [MedicalRecordController::class, 'show']);
});