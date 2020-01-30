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
            // justificacion incidencia 
            $table->string('justificacion')->comment('Razon de la incidencia');
            // fecha inicio de la incidencia 
            $table->dateTime('fecha_inicio')->comment('Fecha inicio de la incidencia');
            // fecha fin de la incidencia 
            $table->dateTime('fecha_fin')->comment('Fecha fin de la incidencia');
            // LLave foranea que apunta al objeto que se asigna la incidencia
            $table->bigInteger('incidenciable_id')->comment('FK que apunta al objeto que se asigna la incidencia');
            // Clase del objeto al que se está asignando la incidencia
            $table->string('incidenciable_type')->comment('Nombre de la clase del objeto al que se esta asignando la incidencia');
            $table->index(['incidenciable_id', 'incidenciable_type']);
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'incidencias';
        $comentario_tabla = 'Tabla donde se almacenan los horarios en los que no se podrá asignar una audiencia para centros, salas y conciliadores.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
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
