<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\SessionController;

Route::get('/', function () {
    return view('welcome');
});

// ALUMNOS
Route::get('/alumnos',        [AlumnoController::class, 'index']);
Route::post('/alumnos',       [AlumnoController::class, 'store']);
Route::get('/alumnos/{id}',   [AlumnoController::class, 'show']);
Route::put('/alumnos/{id}',   [AlumnoController::class, 'update']);
Route::delete('/alumnos/{id}',[AlumnoController::class, 'destroy']);
Route::post('/alumnos/{id}/email', [AlumnoController::class, 'sendEmail']);

// Cubrimos varios nombres de endpoint posibles
Route::post('/alumnos/{id}/session',         [SessionController::class, 'login']);   // createSession
Route::post('/alumnos/{id}/session/login',   [SessionController::class, 'login']);
Route::post('/alumnos/{id}/session/create',  [SessionController::class, 'login']);

Route::post('/alumnos/{id}/session/verify',  [SessionController::class, 'verify']);
Route::post('/alumnos/{id}/session/logout',  [SessionController::class, 'logout']);


// PROFESORES
Route::get('/profesores',         [ProfesorController::class, 'index']);
Route::post('/profesores',        [ProfesorController::class, 'store']);
Route::get('/profesores/{id}',    [ProfesorController::class, 'show']);
Route::put('/profesores/{id}',    [ProfesorController::class, 'update']);
Route::delete('/profesores/{id}', [ProfesorController::class, 'destroy']);

// Foto de perfil: cubrimos varios posibles paths
Route::post('/alumnos/{id}/fotoPerfil', [AlumnoController::class, 'uploadFotoPerfil']);
Route::post('/alumnos/{id}/profile-picture', [AlumnoController::class, 'uploadFotoPerfil']);
Route::post('/alumnos/{id}/foto-perfil', [AlumnoController::class, 'uploadFotoPerfil']);

