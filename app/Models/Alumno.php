<?php

namespace App\Models;

class Alumno
{
    public $id;
    public $nombres;
    public $apellidos;
    public $matricula;
    public $promedio;

    public function __construct($id, $nombres, $apellidos, $matricula, $promedio)
    {
        $this->id = $id;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->matricula = $matricula;
        $this->promedio = $promedio;
    }
}