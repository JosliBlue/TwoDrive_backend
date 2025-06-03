<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('papelera', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_archivo');
            $table->dateTime('fecha_eliminacion');
            $table->unique(['id_usuario', 'id_archivo']);
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->foreign('id_archivo')->references('id_archivo')->on('archivos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('papelera');
    }
};
