<?php

use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'registrar']);
    Route::post('/login', [AuthController::class, 'iniciarSesion']);
    Route::post('/verify-2fa', [AuthController::class, 'verificar2fa']);
    Route::post('/forgot-password', [AuthController::class, 'enviarCodigoRecuperacion']);
    Route::post('/reset-password', [AuthController::class, 'restablecerContrasena']);
});


Route::middleware([IsUserAuth::class])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/profile', [AuthController::class, 'perfil']);
        Route::post('/enable-2fa', [AuthController::class, 'habilitar2fa']);
        Route::post('/enviar-codigo-2fa', [AuthController::class, 'enviarCodigo2fa']);
        Route::post('/cerrar-sesion', [AuthController::class, 'cerrarSesion']);
    });
    // Rutas para archivos
});
Route::prefix('archivos')->group(function () {
    Route::get('/', [ArchivosController::class, 'index']); // Obtener archivos del usuario autenticado
    Route::get('/trash', [ArchivosController::class, 'trash']); // Obtener archivos en la papelera
    Route::post('/upload', [ArchivosController::class, 'store']); // Subir múltiples archivos
    Route::get('/download/{id}', [ArchivosController::class, 'descargar']); // Descargar un archivo
    Route::delete('/delete/{id}', [ArchivosController::class, 'destroy']); // Eliminar un archivo
});



// Rutas para compartir archivos
Route::prefix('archivos_compartidos')->group(function () {
    Route::post('/{id}/enviar', [\App\Http\Controllers\ArchivosCompartidosController::class, 'enviar']);
    Route::get('/{token}', [\App\Http\Controllers\ArchivosCompartidosController::class, 'accesoPorToken']);
    Route::post('/{id}/revocar', [\App\Http\Controllers\ArchivosCompartidosController::class, 'revocar']);
});

// Rutas de usuario
Route::prefix('users')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'index']);
    Route::get('/{id}', [\App\Http\Controllers\UserController::class, 'show']);
    Route::put('/{id}', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
});

// Rutas de logs
Route::prefix('logs')->group(function () {
    Route::get('/', [\App\Http\Controllers\LogsController::class, 'index']);
    Route::get('/{id}', [\App\Http\Controllers\LogsController::class, 'show']);
});
