<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConciliadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conciliadores', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('Pk de la tabla conciliadores');
            // id de la persona con rol de conciliador 
            $table->integer('persona_id')->comment('FK de la tabla personas');
            $table->foreign('persona_id')->references('id')->on('personas');
            // id del centro asignado
            $table->integer('centro_id')->comment('FK de la tabla centros');
            $table->foreign('centro_id')->references('id')->on('centros');
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
        Schema::dropIfExists('conciliadores');
    }
}
