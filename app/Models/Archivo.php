<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;
    protected $table = 'archivos';
    protected $primaryKey = 'id_archivo';
    public $timestamps = false;
    protected $fillable = [
        'nombre_original',
        'nombre_guardado',
        'hash_sha256',
        'tipo_archivo',
        'tamanio',
        'id_usuario',
        'fecha_subida',
        'es_publico',
        'en_papelera',
        'fecha_eliminacion',
    ];
}
