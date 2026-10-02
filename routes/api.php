<?php

use App\Http\Controllers\AnimalController;
use Illuminate\Support\Facades\Route;

Route::get('/animal', [AnimalController::class, 'index'])
    ->middleware('permission:animals.view');

Route::get('/animal/{code}', [AnimalController::class, 'show'])
    ->middleware('permission:animals.view');

Route::post('/animal', [AnimalController::class, 'store'])
    ->middleware('permission:animals.create');

Route::put('/animal/{code}', [AnimalController::class, 'update'])
    ->middleware('permission:animals.update');
