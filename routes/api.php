<?php

use App\Http\Controllers\AnimalController;
use Illuminate\Support\Facades\Route;

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