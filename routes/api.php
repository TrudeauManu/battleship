<?php

use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('parties')
    ->controller(PartieController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{partie}', 'show');
        Route::post('/', 'store');
        Route::put('/{partie}', 'update');
        Route::delete('/{partie}', 'destroy');
    });
