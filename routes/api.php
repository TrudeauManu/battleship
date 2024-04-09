<?php

use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('battleship-ai/parties')
    ->controller(PartieController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', 'index'); // A enlever
        Route::get('/{partie}', 'show'); // A enlever
        Route::post('/', 'store');
        Route::delete('/{partie}', 'destroy');
        Route::post('/{partie}/missiles', 'shoot');
        Route::put('/{partie}/missiles/{coordonnee}', 'updateMissile');
    });
