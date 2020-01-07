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
            $table->bigIncrements('id');
            // fecha inicio de la incidencia 
            $table->date('fecha_inicio');
            // fecha fin de la incidencia 
            $table->date('fecha_fin');
            // hora inicio de la incidencia 
            $table->time('hora_inicio');
            // hora fin de la incidencia 
            $table->time('hora_fin');
            // LLave foranea que apunta al objeto que se asigna la incidencia
            $table->bigInteger('incidenciable_id');
            // Clase del objeto al que se está asignando la incidencia
            $table->string('incidenciable_type');
            $table->index(['incidenciable_id', 'incidenciable_type']);
            $table->softDeletes();
            $table->timestamps();;
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
