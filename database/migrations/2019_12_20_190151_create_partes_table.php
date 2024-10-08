<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartesTable extends Migration
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
            $table->unsignedBigInteger('genero_id')->nullable()->comment('FK de la tabla partes');
            // LLave foranea a tipo Persona
            $table->unsignedBigInteger('tipo_persona_id')->comment('FK de la tabla tipo_personas');
            // LLave foranea a nacionalidad
            $table->unsignedBigInteger('nacionalidad_id')->nullable()->comment('FK de la tabla nacionalidades');
            //Llave foranea a entidad de nacimiento
            $table->char('entidad_nacimiento_id',2)->nullable()->comment('FK de la tabla estados');

            $table->string('nombre')->nullable()->comment('Nombre de la parte')->index();
            $table->string('primer_apellido')->nullable()->comment('Primer apelldo de la parte')->index();
            $table->string('segundo_apellido')->nullable()->comment('Segundo apellido de la parte')->index();
            $table->string('nombre_comercial')->nullable()->comment('Nombre de persona moral')->index();
            $table->date('fecha_nacimiento')->nullable()->comment('Fecha de nacimiento de la parte');
            $table->string('edad')->nullable()->comment('Edad de la parte');
            $table->string('rfc')->nullable()->comment('RFC de la parte')->index();
            $table->string('curp')->nullable()->comment('Curp de la parte')->index();
            $table->boolean('padece_discapacidad')->nullable()->comment('Indicador de discapacidad');
            $table->unsignedBigInteger('tipo_discapacidad_id')->nullable()->comment('FK de la tabla tipo_discapacidades');
            $table->foreign('tipo_discapacidad_id')->references('id')->on('tipo_discapacidades');
            $table->boolean('publicacion_datos')->nullable()->comment('Indicador donde se expresa si la parte desdea publicar sus datos')->index();
            // Datos Para representantes legales
            $table->string('instrumento')->nullable()->comment('Instrumento con que acredita ser representante legal');
            $table->date('feha_instrumento')->nullable()->comment('Fecha del instrumento');
            $table->string('numero_notaria')->nullable()->comment('Número de notaría que acredita la representatividad');
            $table->string('localidad_notaria')->nullable()->comment('Localidad de la notaría que acredita la representatividad');
            $table->string('nombre_notario')->nullable()->comment('Nombre completo del notario encargado de la notaría que acredita la representatividad');
            $table->boolean('representante')->nullable()->comment('Indicador de representatividad');
            $table->integer('parte_representada_id')->nullable()->comment('id de ka paere representada');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();

            // Agrego referencia a Llave foranea a tabla Generos
            $table->foreign('genero_id')->references('id')->on('generos');

        });
        $tabla_nombre = 'partes';
        $comentario_tabla = 'Tabla donde se almacenan los nombres de los involucrados en el sistema.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
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
