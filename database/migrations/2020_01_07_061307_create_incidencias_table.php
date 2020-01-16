<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidencias', function (Blueprint $table) {
            // llave principal
            $table->bigIncrements('id')->comment('PK de la tabla incidencias');
            // fecha inicio de la incidencia 
            $table->date('fecha_inicio')->comment('Fecha inicio de la incidencia');
            // fecha fin de la incidencia 
            $table->date('fecha_fin')->comment('Fecha fin de la incidencia');
            // hora inicio de la incidencia 
            $table->time('hora_inicio')->comment('Hora inicio de la incidencia');
            // hora fin de la incidencia 
            $table->time('hora_fin')->comment('Hora fin de la incidencia');
            // LLave foranea que apunta al objeto que se asigna la incidencia
            $table->bigInteger('incidenciable_id')->comment('FK que apunta al objeto que se asigna la incidencia');
            // Clase del objeto al que se está asignando la incidencia
            $table->string('incidenciable_type')->comment('Nombre de la clase del objeto al que se esta asignando la incidencia');
            $table->index(['incidenciable_id', 'incidenciable_type']);
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidencias');
    }
}
