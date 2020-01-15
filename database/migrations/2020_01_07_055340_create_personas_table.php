<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->bigIncrements('id');
            // nombre de la persona
            $table->string('nombre');
            // apellido paterno de la persona
            $table->string('paterno')->nullable();
            // apellido materno de la persona
            $table->string('materno')->nullable();
            // razon social en caso de ser persona moral
            $table->string('razon_social')->nullable();
            // fecha de nacimiento de la persona
            $table->string('curp')->nullable();
			// fecha de nacimiento de la persona
            $table->string('rfc');
			// fecha de nacimiento de la persona
            $table->date('fecha_nacimiento')->nullable();
            // id del tipo persona
            $table->integer('tipo_persona_id');
            $table->foreign('tipo_persona_id')->references('id')->on('tipo_personas');
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
        Schema::dropIfExists('personas');
    }
}
