<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AlumnoController extends Controller
{
    private static array $alumnos = [];
    private static string $filePath = '';

    private static function getFilePath(): string
    {
        if (empty(self::$filePath)) {
            self::$filePath = storage_path('app/alumnos.json');
        }
        return self::$filePath;
    }

    private static function loadData(): void
    {
        $path = self::getFilePath();
        if (!file_exists($path)) {
            file_put_contents($path, json_encode([]));
        }
        self::$alumnos = json_decode(file_get_contents($path), true) ?? [];
    }

    private static function saveData(): void
    {
        file_put_contents(self::getFilePath(), json_encode(self::$alumnos, JSON_PRETTY_PRINT));
    }

    public function index(): JsonResponse
    {
        self::loadData();
        return response()->json(array_values(self::$alumnos), 200);
    }

    public function store(Request $request): JsonResponse
    {
        self::loadData();

        try {
            $validated = $request->validate([
                'id'        => 'required|integer|min:1',
                'nombres'   => 'required|string',
                'apellidos' => 'required|string',
                'matricula' => 'required|string',
                'promedio'  => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $e->errors(),
            ], 400);
        }

        self::$alumnos[] = $validated;
        self::saveData();

        return response()->json($validated, 201);
    }

    public function show($id): JsonResponse
    {
        self::loadData();
        $id = (int)$id;

        foreach (self::$alumnos as $alumno) {
            if ((int)$alumno['id'] === $id) {
                return response()->json($alumno, 200);
            }
        }

        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }

    public function update(Request $request, $id): JsonResponse
    {
        self::loadData();
        $id = (int)$id;

        // validación para campos incorrectos
        try {
            $request->validate([
                'nombres'   => 'sometimes|required|string',
                'apellidos' => 'sometimes|required|string',
                'matricula' => 'sometimes|required|string',
                'promedio'  => 'sometimes|required|numeric|min:0',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $e->errors(),
            ], 400);
        }

        $data = $request->all();
        foreach (self::$alumnos as &$alumno) {
            if ((int)$alumno['id'] === $id) {
                $alumno = array_merge($alumno, $data);
                self::saveData();
                return response()->json($alumno, 200);
            }
        }

        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }

    public function destroy($id): JsonResponse
    {
        self::loadData();
        $id = (int)$id;

        foreach (self::$alumnos as $index => $alumno) {
            if ((int)$alumno['id'] === $id) {
                unset(self::$alumnos[$index]);
                self::saveData();
                return response()->json(['message' => 'Eliminado'], 200);
            }
        }

        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }
}
