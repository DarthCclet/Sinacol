<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partes', function (Blueprint $table) {
            $table->bigIncrements('id');
            //LLave foranea a generos
            $table->unsignedBigInteger('solicitud_id');
            //LLave foranea a generos
            $table->unsignedBigInteger('tipo_parte_id');
            //LLave foranea a generos
            $table->unsignedBigInteger('genero_id');
            // LLave foranea a tipo Persona
            $table->unsignedBigInteger('tipo_persona_id');
            // LLave foranea a nacionalidad
            $table->unsignedBigInteger('nacionalidad_id');
            //Llave foranea a entidad de nacimiento
            $table->char('entidad_nacimiento_id',2);

            $table->string('nombre');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('edad')->nullable();
            $table->string('rfc');
            $table->string('curp')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Agrego referencia a Llave foranea a tabla Generos
            $table->foreign('genero_id')->references('id')->on('generos');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partes');
    }
}
