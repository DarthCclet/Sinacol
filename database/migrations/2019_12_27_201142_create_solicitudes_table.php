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
            $table->bigIncrements('id')->comment('PK de la tabla solicitudes');
            $table->unsignedBigInteger('abogado_id')->comment('FK de la tabla abogados');
            $table->unsignedBigInteger('estatus_solicitud_id')->comment('FK de la tabla estatus_solicitudes');
            $table->unsignedBigInteger('motivo_solicitud_id')->comment('FK de la tabla motivo_solicitudes');
            $table->unsignedBigInteger('centro_id')->comment('FK de la tabla centros');
            $table->unsignedBigInteger('user_id')->comment('FK de la tabla users');
            $table->boolean('ratificada')->comment('Indica si la solicitud fue ratificada');
            $table->dateTime('fecha_ratificacion')->comment('Indica la fecha de ratificacion');
            $table->dateTime('fecha_recepcion')->comment('Indica la fecha en que se recibio la solicitud');
            $table->string('observaciones')->comment('Aqui se agregan las observaciones de la solicitud');
            $table->boolean('presenta_abogado')->comment('Indica si se presenta el aborado');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
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
