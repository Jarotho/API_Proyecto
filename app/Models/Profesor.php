<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;

    protected $table = 'profesores';

    // Eloquent ya maneja 'id' autoincremental por defecto
    protected $fillable = [
        'numeroEmpleado',
        'nombres',
        'apellidos',
        'horasClase',
    ];
}
