<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProfesorController extends Controller
{
    private static array $profesores = [];
    private static string $filePath = '';

    private static function getFilePath(): string
    {
        if (empty(self::$filePath)) {
            self::$filePath = storage_path('app/profesores.json');
        }
        return self::$filePath;
    }

    private static function loadData(): void
    {
        $path = self::getFilePath();
        if (!file_exists($path)) {
            file_put_contents($path, json_encode([]));
        }
        self::$profesores = json_decode(file_get_contents($path), true) ?? [];
    }

    private static function saveData(): void
    {
        file_put_contents(self::getFilePath(), json_encode(self::$profesores, JSON_PRETTY_PRINT));
    }

    public function index(): JsonResponse
    {
        self::loadData();
        return response()->json(array_values(self::$profesores), 200);
    }

    public function store(Request $request): JsonResponse
    {
        self::loadData();

        try {
            $validated = $request->validate([
                'id'            => 'required|integer|min:1',
                'nombres'       => 'required|string',
                'apellidos'     => 'required|string',
                'numeroEmpleado'=> 'required|integer|min:1',
                'horasClase'    => 'required|integer|min:0|max:50',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $e->errors(),
            ], 400);
        }

        self::$profesores[] = $validated;
        self::saveData();

        return response()->json($validated, 201);
    }

    public function show($id): JsonResponse
    {
        self::loadData();
        $id = (int)$id;

        foreach (self::$profesores as $profesor) {
            if ((int)$profesor['id'] === $id) {
                return response()->json($profesor, 200);
                // OJO: si algún test espera 200, cámbialo a 200.
            }
        }

        return response()->json(['message' => 'Profesor no encontrado'], 404);
    }

public function update(Request $request, $id): JsonResponse
{
    self::loadData();
    $id = (int)$id;

    try {
        $request->validate([
            'nombres'        => 'sometimes|required|string',
            'apellidos'      => 'sometimes|required|string',
            'numeroEmpleado' => 'sometimes|required|integer|min:1',
            'horasClase'     => 'sometimes|required|integer|min:0|max:50',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Errores de validación',
            'errors'  => $e->errors(),
        ], 400);
    }

    $data = $request->all();
    foreach (self::$profesores as &$profesor) {
        if ((int)$profesor['id'] === $id) {
            $profesor = array_merge($profesor, $data);
            self::saveData();
            return response()->json($profesor, 200);
        }
    }

    return response()->json(['message' => 'Profesor no encontrado'], 404);
}



public function destroy($id): JsonResponse
{
    self::loadData();
    $id = (int)$id;

    foreach (self::$profesores as $index => $profesor) {
        if ((int)$profesor['id'] === $id) {
            unset(self::$profesores[$index]);
            self::saveData();
            return response()->json(['message' => 'Eliminado'], 200);
        }
    }

    return response()->json(['message' => 'Profesor no encontrado'], 404);
}
}
