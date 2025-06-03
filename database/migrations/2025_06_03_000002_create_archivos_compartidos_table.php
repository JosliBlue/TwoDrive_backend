<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('archivos_compartidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_archivo');
            $table->unsignedBigInteger('id_propietario');
            $table->unsignedBigInteger('id_receptor');
            $table->enum('permisos', ['lectura','descarga','edicion','propietario'])->default('lectura');
            $table->timestamp('fecha_compartido')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('expiracion')->nullable();
            $table->string('token_acceso', 64)->nullable();
            $table->foreign('id_archivo')->references('id_archivo')->on('archivos')->onDelete('cascade');
            $table->foreign('id_propietario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_receptor')->references('id')->on('users')->onDelete('cascade');
            $table->index('token_acceso');
        });
    }

    public function down()
    {
        Schema::dropIfExists('archivos_compartidos');
    }
};
