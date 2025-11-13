<?php

namespace App\Models;

class Profesor
{
    public $id;
    public $numeroEmpleado;
    public $nombres;
    public $apellidos;
    public $horasClase;

    public function __construct($id, $numeroEmpleado, $nombres, $apellidos, $horasClase)
    {
        $this->id = $id;
        $this->numeroEmpleado = $numeroEmpleado;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->horasClase = $horasClase;
    }
}