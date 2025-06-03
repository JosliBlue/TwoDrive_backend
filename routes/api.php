<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

Route::post('/registrar', [AuthController::class, 'registrar']);
Route::post('/iniciar-sesion', [AuthController::class, 'iniciarSesion']);
Route::post('/verificar-2fa', [AuthController::class, 'verificar2fa']);
Route::post('/enviar-codigo-recuperacion', [AuthController::class, 'enviarCodigoRecuperacion']);
Route::post('/restablecer-contrasena', [AuthController::class, 'restablecerContrasena']);

Route::middleware([IsUserAuth::class])->group(function () {
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/habilitar-2fa', [AuthController::class, 'habilitar2fa']);
    Route::post('/enviar-codigo-2fa', [AuthController::class, 'enviarCodigo2fa']);
    Route::post('/cerrar-sesion', [AuthController::class, 'cerrarSesion']);
});
