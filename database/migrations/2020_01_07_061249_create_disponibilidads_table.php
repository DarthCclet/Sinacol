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
            $table->bigIncrements('id'); 
            // dia disponible
            $table->bigInteger('dia');
            // Hora inicio de actividad del dia
            $table->time('hora_inicio');
            // Hora fin de actividad del dia
            $table->time('hora_fin');
            // LLave foranea que apunta al objeto que se asigna la disponibilidad
            $table->bigInteger('disponibiliable_id');
            // Clase del objeto al que se está asignando la disponibilidad
            $table->string('disponibiliable_type');
            $table->index(['disponibiliable_id', 'disponibiliable_type']);
            $table->softDeletes();
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
