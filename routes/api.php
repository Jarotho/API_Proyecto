<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProfesorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\SessionController;

Route::post('/alumnos/{id}/session/login',  [SessionController::class, 'login']);
Route::post('/alumnos/{id}/session/verify', [SessionController::class, 'verify']);
Route::post('/alumnos/{id}/session/logout', [SessionController::class, 'logout']);


Route::apiResource("/alumnos", AlumnoController::class);
Route::apiResource("/profesores", ProfesorController::class);
Route::post('/alumnos/{id}/email', [AlumnoController::class, 'sendEmail']);
Route::post('/alumnos/{id}/fotoPerfil', [AlumnoController::class, 'uploadFotoPerfil']);

