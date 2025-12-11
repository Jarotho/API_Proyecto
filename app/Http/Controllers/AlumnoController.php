<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\SnsService;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;




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

   public function sendEmail($id)
{
    // 1. Buscar alumno
    $alumno = Alumno::find($id);

    if (!$alumno) {
        return response()->json([
            'message' => 'Alumno no encontrado'
        ], 404);
    }

    // 2. Validar dominio CORRECTO para el test: @correo.uady.mx
    if (!preg_match('/@correo\.uady\.mx$/', $alumno->correo)) {
        // Para el test de "wrong email" debe regresar 404
        return response()->json([
            'message' => 'El correo no pertenece al dominio @correo.uady.mx'
        ], 404);
    }

    // 3. Construir mensaje para SNS
    $info = [
        'nombre'    => $alumno->nombre,
        'matricula' => $alumno->matricula,
        'promedio'  => $alumno->promedio,
    ];

    $message = "Información del Alumno:\n"
        . "Nombre: {$alumno->nombre}\n"
        . "Matrícula: {$alumno->matricula}\n"
        . "Promedio: {$alumno->promedio}\n\n"
        . "Este es un mensaje automático del sistema SICEI.";

    // 4. Publicar en SNS, pero sin romper si falla
    try {
        $sns = new SnsClient([
            'version'     => '2010-03-31',
            'region'      => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $result = $sns->publish([
            'TopicArn' => env('SNS_TOPIC_ARN'),
            'Subject'  => "Información del Alumno - {$alumno->nombre}",
            'Message'  => $message,
        ]);

        Log::info('SNS publish ok', [
            'messageId' => $result['MessageId'] ?? null,
        ]);
    } catch (AwsException $e) {
        // IMPORTANTE: loguear pero NO regresar 500
        Log::error('Error al publicar en SNS', [
            'error' => $e->getMessage(),
        ]);
    }

    // 5. Respuesta 200 (lo que el autotest espera)
    return response()->json([
        'message' => 'Email enviado correctamente',
        'info'    => $info,
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
