<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs_actividad', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_archivo')->nullable();
            $table->string('accion', 50)->nullable();
            $table->text('detalle')->nullable();
            $table->timestamp('fecha')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('ip_usuario', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_archivo')->references('id_archivo')->on('archivos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs_actividad');
    }
};
