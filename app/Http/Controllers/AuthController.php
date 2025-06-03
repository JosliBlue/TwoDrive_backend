<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\CodigoVerificacion;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Registrar nuevo usuario
     */
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
        ]);
        return response()->json(['mensaje' => 'Usuario registrado correctamente'], 201);
    }

    /**
     * Iniciar sesión (con soporte 2FA)
     */
    public function iniciarSesion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
        // Si tiene 2FA habilitado, enviar código
        if ($user->is_2fa_enabled) {
            $code = rand(100000, 999999);
            $user->totp_secret = $code;
            $user->save();
            Mail::to($user->email)->send(new CodigoVerificacion($code));
            return response()->json(['mensaje' => 'Se requiere 2FA', 'requiere_2fa' => true]);
        }
        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token, 'usuario' => $user]);
    }

    /**
     * Verificar código 2FA y devolver JWT
     */
    public function verificar2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->totp_secret !== $request->code) {
            return response()->json(['error' => 'Código inválido'], 401);
        }
        $user->totp_secret = null;
        $user->save();
        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token, 'usuario' => $user]);
    }

    /**
     * Enviar código para recuperación de contraseña
     */
    public function enviarCodigoRecuperacion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $code = rand(100000, 999999);
        $user->totp_secret = $code;
        $user->save();
        Mail::to($user->email)->send(new CodigoVerificacion($code));
        return response()->json(['mensaje' => 'Código enviado al correo']);
    }

    /**
     * Restablecer contraseña usando código
     */
    public function restablecerContrasena(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->totp_secret !== $request->code) {
            return response()->json(['error' => 'Código inválido'], 401);
        }
        $user->password_hash = Hash::make($request->password);
        $user->totp_secret = null;
        $user->save();
        return response()->json(['mensaje' => 'Contraseña restablecida correctamente']);
    }

    /**
     * Obtener perfil del usuario autenticado
     */
    public function perfil(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['mensaje' => 'Usuario no autenticado'], 401);
            }
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['mensaje' => 'Token inválido o no enviado'], 401);
        }
    }

    /**
     * Habilitar 2FA para el usuario
     */
    public function habilitar2fa(): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['mensaje' => 'Usuario no autenticado'], 401);
            }
            $user->is_2fa_enabled = true;
            $user->save();
            return response()->json([
                'mensaje' => '2FA habilitado'
            ]);
        } catch (JWTException $e) {
            return response()->json(['mensaje' => 'Token inválido o no enviado'], 401);
        }
    }

    /**
     * Enviar código de setup 2FA al usuario
     */
    public function enviarCodigo2fa(): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['mensaje' => 'Usuario no autenticado'], 401);
            }
            $code = rand(100000, 999999);
            $user->totp_secret = $code;
            $user->save();
            Mail::to($user->email)->send(new CodigoVerificacion($code));
            return response()->json([
                'mensaje' => 'Código de 2FA enviado'
            ]);
        } catch (JWTException $e) {
            return response()->json(['mensaje' => 'Token inválido o no enviado'], 401);
        }
    }

    /**
     * Cerrar sesión (invalidar JWT)
     */
    public function cerrarSesion(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'mensaje' => 'Token no proporcionado'
                ], 401);
            }
            JWTAuth::invalidate($token);
            return response()->json([
                'mensaje' => 'Sesión cerrada correctamente'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'mensaje' => 'Token inválido o no enviado'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al cerrar sesión'
            ], 500);
        }
    }
}
