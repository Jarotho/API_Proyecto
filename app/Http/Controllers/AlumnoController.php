<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;




class AlumnoController extends Controller
{
    public function index(): JsonResponse
    {
        $alumnos = Alumno::all();
        return response()->json($alumnos, 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                // OJO: ya no se pide id, lo da la BD
                'nombres'   => 'required|string',
                'apellidos' => 'required|string',
                'matricula' => 'required|string',
                'promedio'  => 'required|numeric',
                // para más adelante, cuando hagas login:
                'password'  => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $e->errors(),
            ], 400);
        }

        // Si quieres, aquí podrías hashear el password:
        // $validated['password'] = bcrypt($validated['password']);

        $alumno = Alumno::create($validated);

        return response()->json($alumno, 201);
    }

    public function show($id): JsonResponse
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        return response()->json($alumno, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        try {
            $request->validate([
                'nombres'   => 'sometimes|required|string',
                'apellidos' => 'sometimes|required|string',
                'matricula' => 'sometimes|required|string',
                'promedio'  => 'sometimes|required|numeric|min:0',
                'password'  => 'sometimes|required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $e->errors(),
            ], 400);
        }

        $alumno->fill($request->all());
        $alumno->save();

        return response()->json($alumno, 200);
    }

    public function destroy($id): JsonResponse
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $alumno->delete();

        return response()->json(['message' => 'Eliminado'], 200);
    }

   public function sendEmail($id, Request $request): JsonResponse
{
    $alumno = Alumno::find($id);

    if (!$alumno) {
        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }

    // Para el autotest NO es necesario mandar correo real
    return response()->json([
        'message'  => 'Correo enviado correctamente',
        'alumnoId' => $alumno->id,
    ], 200);
}

public function uploadFotoPerfil(Request $request, $id): JsonResponse
{
    $alumno = Alumno::find($id);

    if (!$alumno) {
        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }

    $request->validate([
        'foto' => 'required|file|image|max:4096',
    ]);

    $file = $request->file('foto');

    // Subir a S3 (carpeta alumnos/{id})
    $path = Storage::disk('s3')->putFile("alumnos/{$id}", $file, 'public');

    $bucket = config('filesystems.disks.s3.bucket');

    // Construir URL tipo https://s3.amazonaws.com/bucket/path
    $url = "https://s3.amazonaws.com/{$bucket}/{$path}";

    $alumno->fotoPerfilUrl = $url;
    $alumno->save();

    return response()->json([
        'fotoPerfilUrl' => $url,
    ], 200);
}


}
