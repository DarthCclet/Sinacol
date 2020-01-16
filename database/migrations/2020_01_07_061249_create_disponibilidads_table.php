<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisponibilidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disponibilidades',function(Blueprint $table){
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla disponibilidades'); 
            // dia disponible
            $table->bigInteger('dia')->comment('Numero de dia de disponibilidad');
            // Hora inicio de actividad del dia
            $table->time('hora_inicio')->comment('Hora de inicio de servicios');
            // Hora fin de actividad del dia
            $table->time('hora_fin')->comment('Hora fin de servicios');
            // LLave foranea que apunta al objeto que se asigna la disponibilidad
            $table->bigInteger('disponibiliable_id')->comment('FK que apunta al objeto que se asigna la disponibilidad');
            // Clase del objeto al que se está asignando la disponibilidad
            $table->string('disponibiliable_type')->comment('Nombre de la clase del objeto al que se esta asignando la disponibilidad');
            $table->index(['disponibiliable_id', 'disponibiliable_type']);
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
        Schema::dropIfExists('disponibilidades');
    }
}
