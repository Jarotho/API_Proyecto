<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProfesorController;

Route::get('/', function () {
    return view('welcome');
});

// ALUMNOS
Route::get('/alumnos',        [AlumnoController::class, 'index']);
Route::post('/alumnos',       [AlumnoController::class, 'store']);
Route::get('/alumnos/{id}',   [AlumnoController::class, 'show']);
Route::put('/alumnos/{id}',   [AlumnoController::class, 'update']);
Route::delete('/alumnos/{id}',[AlumnoController::class, 'destroy']);

// PROFESORES
Route::get('/profesores',         [ProfesorController::class, 'index']);
Route::post('/profesores',        [ProfesorController::class, 'store']);
Route::get('/profesores/{id}',    [ProfesorController::class, 'show']);
Route::put('/profesores/{id}',    [ProfesorController::class, 'update']);
Route::delete('/profesores/{id}', [ProfesorController::class, 'destroy']);
