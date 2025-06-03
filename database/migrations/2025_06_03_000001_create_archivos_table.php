<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id('id_archivo');
            $table->string('nombre_original', 255);
            $table->string('nombre_guardado', 255);
            $table->string('hash_sha256', 64);
            $table->string('tipo_archivo', 100);
            $table->unsignedBigInteger('tamanio');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamp('fecha_subida')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('es_publico')->default(false);
            $table->boolean('en_papelera')->default(false);
            $table->timestamp('fecha_eliminacion')->nullable();
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('archivos');
    }
};
