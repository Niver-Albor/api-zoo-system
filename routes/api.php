<?php

use App\Http\Controllers\AnimalController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/animal', AnimalController::class)
    ->parameters(['animal' => 'animal:code']);