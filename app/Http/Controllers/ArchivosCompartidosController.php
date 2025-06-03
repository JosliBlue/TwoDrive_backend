<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArchivosCompartidosController extends Controller
{
    public function enviar(Request $request, $id) {
        // Compartir archivo
    }
    public function accesoPorToken($token) {
        // Acceso a archivo compartido por token
    }
    public function revocar(Request $request, $id) {
        // Revocar acceso compartido
    }
}
