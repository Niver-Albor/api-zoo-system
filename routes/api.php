<?php

use App\Actions\GetPermissionsAction;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\HabitatController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\UserController;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Route;

// Módulos de Animales.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/animals', [AnimalController::class, 'index']);

    Route::get('/animals/{animal}', [AnimalController::class, 'show']);

    Route::post('/animals', [AnimalController::class, 'store']);

    Route::put('/animals/{animal}', [AnimalController::class, 'update']);

    Route::delete('/animals/{animal}', [AnimalController::class, 'destroy']);
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

// Módulo de Especies.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/species', [SpeciesController::class, 'store']);

    Route::put('/species/{species}', [SpeciesController::class, 'update']);

    Route::get('/species', [SpeciesController::class, 'index']);

    Route::get('/species/{species}', [SpeciesController::class, 'show']);

    Route::delete('/species/{species}', [SpeciesController::class, 'destroy']);
});

// Módulo de Habitats.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/habitats', [HabitatController::class, 'store']);

    Route::put('/habitats/{habitat}', [HabitatController::class, 'update']);

    Route::get('/habitats', [HabitatController::class, 'index']);

    Route::get('/habitats/{habitat}', [HabitatController::class, 'show']);

    Route::delete('/habitats/{habitat}', [HabitatController::class, 'destroy']);
});

// Permisos
Route::get('/permissions', GetPermissionsAction::class)
    ->middleware(['auth:sanctum']);

// Módulo de Roles
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/roles', [RoleController::class, 'store']);

    Route::put('/roles/{role}', [RoleController::class, 'update']);

    Route::get('/roles', [RoleController::class, 'index']);

    Route::get('/roles/{role}', [RoleController::class, 'show']);

    Route::delete('roles/{role}', [RoleController::class, 'destroy']);
});