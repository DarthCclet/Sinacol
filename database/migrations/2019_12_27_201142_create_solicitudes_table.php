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
            $table->unsignedBigInteger('estatus_solicitud_id')->comment('FK de la tabla estatus_solicitudes');
            $table->unsignedBigInteger('centro_id')->comment('FK de la tabla centros');
            $table->unsignedBigInteger('user_id')->comment('FK de la tabla users');
            $table->boolean('ratificada')->comment('Indica si la solicitud fue ratificada');
            $table->boolean('solicita_excepcion')->comment('Indica si la solicitud fue ratificada');
            $table->dateTime('fecha_ratificacion')->comment('Indica la fecha de ratificacion');
            $table->dateTime('fecha_recepcion')->comment('Indica la fecha en que se recibio la solicitud');
            $table->string('observaciones')->comment('Aqui se agregan las observaciones de la solicitud');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();

            // Agrego referencia a Llave foranea a tabla Generos
            $table->foreign('estatus_solicitud_id')->references('id')->on('estatus_solicitudes');
            $table->foreign('centro_id')->references('id')->on('centros');
            $table->foreign('user_id')->references('id')->on('users');
        });

        $tabla_nombre = 'solicitudes';
        $comentario_tabla = 'Tabla donde se almacenan las solicitudes de conciliacion.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
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
