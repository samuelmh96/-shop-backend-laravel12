<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

    // Rutas para usuarios
Route::middleware('auth:sanctum')->group(function () {
    // CRUD API REST USUARIOS
    Route::apiResource('/users', UserController::class);

    // CRUD ROLES
    Route::apiResource('role', RoleController::class);
    Route::apiResource('persona', PersonaController::class);
    Route::apiResource('permiso', PermisoController::class);
    Route::apiResource('documento', DocumentoController::class);

});


 // Rutas para autenticación
Route::prefix('/v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'getProfile']);
    });
});
