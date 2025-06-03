<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archivo;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ArchivosController extends Controller
{
    public function index(Request $request)
    {
        // Listar archivos del usuario autenticado (no en papelera)
        $user = JWTAuth::parseToken()->authenticate();
        $archivos = Archivo::where('id_usuario', $user->id)
            ->where('en_papelera', false)
            ->orderByDesc('fecha_subida')
            ->get();
        return response()->json($archivos);
    }

    public function trash(Request $request)
    {
        // Listar archivos en papelera del usuario
        $user = JWTAuth::parseToken()->authenticate();
        $archivos = Archivo::where('id_usuario', $user->id)
            ->where('en_papelera', true)
            ->orderByDesc('fecha_eliminacion')
            ->get();
        return response()->json($archivos);
    }
/**
 * Subir múltiples archivos
 *
 * @OA\Post(
 *     path="/archivos/upload",
 *     summary="Sube múltiples archivos para el usuario autenticado",
 *     tags={"Archivos"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"files"},
 *                 @OA\Property(
 *                     property="files",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         format="binary"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Archivos subidos correctamente"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="No se han subido archivos"
 *     )
 * )
 */
    public function store(Request $request)
    {
        // Subir múltiples archivos
        $user = JWTAuth::parseToken()->authenticate();
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No se han subido archivos'], 400);
        }
        $files = $request->file('files');
        $uploadedFiles = [];
        foreach ($files as $file) {
            $nombreOriginal = $file->getClientOriginalName();
            $nombreGuardado = uniqid() . '-' . $nombreOriginal;
            $hash = hash_file('sha256', $file->getRealPath());
            $tipo = $file->getClientMimeType();
            $tamanio = $file->getSize();
            $file->storeAs('uploads', $nombreGuardado, 'public');
            $archivo = Archivo::create([
                'nombre_original' => $nombreOriginal,
                'nombre_guardado' => $nombreGuardado,
                'hash_sha256' => $hash,
                'tipo_archivo' => $tipo,
                'tamanio' => $tamanio,
                'id_usuario' => $user->id,
                'es_publico' => false,
                'en_papelera' => false
            ]);
            $uploadedFiles[] = [
                'id_archivo' => $archivo->id_archivo,
                'nombre_original' => $nombreOriginal,
                'nombre_guardado' => $nombreGuardado
            ];
        }
        return response()->json([
            'message' => 'Archivos subidos correctamente',
            'files' => $uploadedFiles,
            'total' => count($uploadedFiles)
        ]);
    }

    public function descargar($id)
    {
        // Descargar archivo
        $user = JWTAuth::parseToken()->authenticate();
        $archivo = Archivo::where('id_archivo', $id)->where('id_usuario', $user->id)->first();
        if (!$archivo) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
        $path = storage_path('app/public/uploads/' . $archivo->nombre_guardado);
        if (!file_exists($path)) {
            return response()->json(['error' => 'Archivo físico no encontrado'], 404);
        }
        return response()->download($path, $archivo->nombre_original);
    }

    public function destroy($id)
    {
        // Eliminar archivo (mover a papelera)
        $user = JWTAuth::parseToken()->authenticate();
        $archivo = Archivo::where('id_archivo', $id)->where('id_usuario', $user->id)->first();
        if (!$archivo) {
            return response()->json(['error' => 'Archivo no encontrado o no autorizado'], 404);
        }
        $archivo->en_papelera = true;
        $archivo->fecha_eliminacion = now();
        $archivo->save();
        return response()->json(['message' => 'Archivo movido a la papelera']);
    }
}
