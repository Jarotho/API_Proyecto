<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoSession extends Model
{
    protected $fillable = [
        'alumno_id',
        'sessionString',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
