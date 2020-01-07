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
            $table->bigIncrements('id');
            // id de la persona con rol de conciliador 
            $table->integer('persona_id');
            $table->foreign('persona_id')->references('id')->on('personas');
            // id del centro asignado
            $table->integer('centro_id');
            $table->foreign('centro_id')->references('id')->on('centros');
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
        Schema::dropIfExists('conciliadores');
    }
}
