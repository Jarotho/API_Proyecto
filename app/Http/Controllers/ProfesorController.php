<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfesorController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Profesor::all(), 200);
    }

    public function show($id): JsonResponse
    {
        $profesor = Profesor::find($id);

        if (!$profesor) {
            return response()->json(['message' => 'Profesor no encontrado'], 404);
        }

        return response()->json($profesor, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        // Campos mínimos que el autotest manda
        $required = ['nombres', 'apellidos', 'numeroEmpleado', 'horasClase'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                // Faltan campos → 400
                return response()->json(['message' => 'Datos incompletos'], 400);
            }
        }

        if (!is_numeric($data['numeroEmpleado']) || !is_numeric($data['horasClase'])) {
            // Tipos incorrectos → 400
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        // Casteamos a enteros y guardamos
        $profesor = Profesor::create([
            'nombres'        => $data['nombres'],
            'apellidos'      => $data['apellidos'],
            'numeroEmpleado' => (int) $data['numeroEmpleado'],
            'horasClase'     => (int) $data['horasClase'],
        ]);

        return response()->json($profesor, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $profesor = Profesor::find($id);

        if (!$profesor) {
            return response()->json(['message' => 'Profesor no encontrado'], 404);
        }

        $data = $request->all();

        $required = ['nombres', 'apellidos', 'numeroEmpleado', 'horasClase'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }
        }

        if (!is_numeric($data['numeroEmpleado']) || !is_numeric($data['horasClase'])) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $profesor->update([
            'nombres'        => $data['nombres'],
            'apellidos'      => $data['apellidos'],
            'numeroEmpleado' => (int) $data['numeroEmpleado'],
            'horasClase'     => (int) $data['horasClase'],
        ]);

        return response()->json($profesor, 200);
    }

    public function destroy($id): JsonResponse
    {
        $profesor = Profesor::find($id);

        if (!$profesor) {
            return response()->json(['message' => 'Profesor no encontrado'], 404);
        }

        $profesor->delete();

        return response()->json(['message' => 'Profesor eliminado'], 200);
    }
}
