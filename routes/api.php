<?php

use App\Http\Controllers\MissileController;
use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('parties')
    ->controller(PartieController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/', 'store');
        Route::delete('/{partie}', 'destroy');
    });

Route::prefix('parties')
    ->controller(MissileController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/{partie}/missiles', 'shoot');
        Route::put('/{partie}/missiles/{coordonnee}', 'updateMissile');
    });


