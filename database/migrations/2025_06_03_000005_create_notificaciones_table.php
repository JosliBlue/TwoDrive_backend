<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->enum('tipo', ['archivo_compartido','nuevo_comentario','solicitud_acceso']);
            $table->text('mensaje');
            $table->unsignedBigInteger('id_referencia')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_creacion')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
};
