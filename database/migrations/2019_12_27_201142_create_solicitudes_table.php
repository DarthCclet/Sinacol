<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('abogado_id');
            $table->unsignedBigInteger('estatus_solicitud_id');
            $table->unsignedBigInteger('motivo_solicitud_id');
            $table->unsignedBigInteger('centro_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('ratificada');
            $table->dateTime('fecha_ratificacion');
            $table->dateTime('fecha_recepcion');
            $table->string('observaciones');
            $table->boolean('presenta_abogado');
            $table->softDeletes();
            $table->timestamps();

            // Agrego referencia a Llave foranea a tabla Generos
            $table->foreign('abogado_id')->references('id')->on('abogados');
            $table->foreign('estatus_solicitud_id')->references('id')->on('estatus_solicitudes');
            $table->foreign('motivo_solicitud_id')->references('id')->on('motivo_solicitudes');
            $table->foreign('centro_id')->references('id')->on('centros');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes');
    }
}
