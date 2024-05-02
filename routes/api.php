<?php

use App\Http\Controllers\MissileController;
use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Routes pour créer et détruire une partie.
 */
Route::prefix('parties')
    ->controller(PartieController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/', 'store');
        Route::delete('/{partie}', 'destroy');
    });

/**
 * Routes pour créer et updater un missile.
 */
Route::prefix('parties')
    ->controller(MissileController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/{partie}/missiles', 'store');
        Route::put('/{partie}/missiles/{coordonnee}', 'update');
    });


