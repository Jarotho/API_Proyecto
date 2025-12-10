<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';

    protected $fillable = [
        'nombres',
        'apellidos',
        'matricula',
        'promedio',
        'fotoPerfilUrl', // ya previsto para la parte de S3
        'password',      // previsto para login/sesiones
    ];

    // Si quieres ocultar el password en respuestas JSON:
    protected $hidden = [
        'password',
    ];
}
