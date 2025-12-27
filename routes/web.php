<?php

use App\Http\Controllers\EmpleadoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('employees');
});

Route::get('/api/empleados', [EmpleadoController::class, 'index']);
Route::get('/api/empleados/{id}', [EmpleadoController::class, 'show']);
Route::post('/api/empleados', [EmpleadoController::class, 'store']);
Route::put('/api/empleados/{id}', [EmpleadoController::class, 'update']);
Route::delete('/api/empleados/{id}', [EmpleadoController::class, 'destroy']);
Route::get('/api/areas', [EmpleadoController::class, 'getAreas']);
Route::get('/api/roles', [EmpleadoController::class, 'getRoles']);
