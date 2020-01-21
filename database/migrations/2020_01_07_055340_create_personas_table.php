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
            $table->bigIncrements('id')->comment('Pk de la tabla personas');
            // nombre de la persona
            $table->string('nombre')->comment('Nombre de la persona');
            // apellido paterno de la persona
            $table->string('primer_apellido')->nullable()->comment('Primer apellido de la persona');
            // apellido materno de la persona
            $table->string('segundo_apellido')->nullable()->comment('Segundo apellido de la persona');
            // razon social en caso de ser persona moral
            $table->string('razon_social')->nullable()->comment('nombre de la razon social de la persona');
            // fecha de nacimiento de la persona
            $table->string('curp')->nullable()->comment('Clave unica de registro de poblacion de la persona');
			// fecha de nacimiento de la persona
            $table->string('rfc')->comment('Registro federal de contribuyente de la persona');
			// fecha de nacimiento de la persona
            $table->date('fecha_nacimiento')->nullable()->comment('Fecha de nacimiento de la persona');
            // id del tipo persona
            $table->integer('tipo_persona_id')->comment('FK de la tabla tipos_personas');
            $table->foreign('tipo_persona_id')->references('id')->on('tipo_personas');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'personas';
        $comentario_tabla = 'Tabla donde se almacenan los datos generales de personas que tengan un rol en el sistema.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
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
