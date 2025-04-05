<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);  // Ruta para el registro


Route::middleware('auth:sanctum')->group(function ()
{
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/multimedia', [App\Http\Controllers\Api\ApiMultimediaController::class, 'store']); // Crear
    Route::get('/multimedia', [App\Http\Controllers\Api\ApiMultimediaController::class, 'index']); // Veure tots
    Route::get('/multimedia/user', [App\Http\Controllers\Api\ApiMultimediaController::class, 'showByUser']); // Veure per usuari
    Route::delete('/multimedia/{id}', [App\Http\Controllers\Api\ApiMultimediaController::class, 'destroy']); // Eliminar
});

