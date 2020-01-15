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
            $table->bigIncrements('id')->comment('PK de la tabla partes');
            //LLave foranea a generos
            $table->unsignedBigInteger('solicitud_id')->comment('FK de la tabla solicitudes');
            //LLave foranea a generos
            $table->unsignedBigInteger('tipo_parte_id')->comment('FK de la tabla tipo_partes');
            //LLave foranea a generos
            $table->unsignedBigInteger('genero_id')->comment('FK de la tabla partes');
            // LLave foranea a tipo Persona
            $table->unsignedBigInteger('tipo_persona_id')->comment('FK de la tabla tipo_personas');
            // LLave foranea a nacionalidad
            $table->unsignedBigInteger('nacionalidad_id')->comment('FK de la tabla nacionalidades');
            //Llave foranea a entidad de nacimiento
            $table->char('entidad_nacimiento_id',2)->comment('FK de la tabla estados');

            $table->string('nombre')->comment('Nombre de la parte');
            $table->string('primer_apellido')->nullable()->comment('Primer apelldo de la parte');
            $table->string('segundo_apellido')->nullable()->comment('Segundo apellido de la parte');
            $table->string('nombre_comercial')->nullable()->comment('Nombre de persona moral');
            $table->date('fecha_nacimiento')->comment('Fecha de nacimiento de la parte');
            $table->string('edad')->nullable()->comment('Edad de la parte');
            $table->string('rfc')->comment('RFC de la parte');
            $table->string('curp')->nullable()->comment('Curp de la parte');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
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
