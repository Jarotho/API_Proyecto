<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function login($id, Request $request): JsonResponse
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $data = $request->validate([
            'password' => 'required|string',
        ]);

        // El test manda exactamente el mismo password que se guardó
        if ($data['password'] !== $alumno->password) {
            return response()->json(['message' => 'Credenciales inválidas'], 400);
        }

        // Crear SessionString de 128 caracteres
        $sessionString = Str::random(128);

        AlumnoSession::where('alumno_id', $alumno->id)->delete();

        AlumnoSession::create([
            'alumno_id'     => $alumno->id,
            'sessionString' => $sessionString,
            // si quieres expiración real: Carbon::now()->addMinutes(30)
            'expires_at'    => null,
        ]);

        return response()->json([
            'sessionString' => $sessionString,
        ], 200);
    }

    public function verify($id, Request $request): JsonResponse
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $data = $request->validate([
            'sessionString' => 'required|string|size:128',
        ]);

        $session = AlumnoSession::where('alumno_id', $alumno->id)
            ->where('sessionString', $data['sessionString'])
            ->first();

        if (!$session) {
            return response()->json(['message' => 'Sesión inválida'], 400);
        }

        // Si quieres expirar por tiempo:
        // if ($session->expires_at && $session->expires_at->isPast()) {
        //     return response()->json(['message' => 'Sesión expirada'], 400);
        // }

        return response()->json(['message' => 'Sesión válida'], 200);
    }

    public function logout($id, Request $request): JsonResponse
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $data = $request->validate([
            'sessionString' => 'required|string|size:128',
        ]);

        $session = AlumnoSession::where('alumno_id', $alumno->id)
            ->where('sessionString', $data['sessionString'])
            ->first();

        if ($session) {
            $session->delete();
        }

        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
}
