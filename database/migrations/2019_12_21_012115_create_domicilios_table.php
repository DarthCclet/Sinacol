<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomiciliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Esta estructura está basado en el "ACUERDO por el que aprueba la Norma Técnica sobre Domicilios Geográficos."
         * que aparece en el DOF: 12/11/2010
         */

        Schema::create('domicilios', function (Blueprint $table) {

            $table->bigIncrements('id')->comment('Llave primaria del registro de Domicilios');

            // LLave foranea que apunta al objeto que se asigna el domicilio
            $table->bigInteger('domiciliable_id')->comment('Llave foránea que relaciona un domicilio con un registro domiciliable');

            // Clase del objeto al que se está asignando el domicilio
            $table->string('domiciliable_type')->comment('Es la clase del tipo de objeto domiciliable');

            // Tipo de vialidad
            $table->string('tipo_vialidad')->comment('Nombre del tipo de vialidad');

            // Id del tipo de vialidad en el catálogo de tipos de vialidades
            $table->integer('tipo_vialidad_id')->comment('Llave foránea que relaciona el tipo de vialidad al que pertenece el registro');
            $table->foreign('tipo_vialidad_id')->references('id')->on('tipo_vialidades');

            // Nombre de la vialidad (o calle)
            $table->string('vialidad')->comment('Nombre de la vialidad');

            // Número exterior
            $table->string('num_ext')->comment('Número exterior del domicilio');

            // Número interior
            $table->string('num_int')->nullable()->comment('Número o referencia interior del domicilio');

            // Tipo de asentamiento (del catálogo de tipos de asentamiento)
            $table->string('tipo_asentamiento')->nullable()->comment('Nombre del tipo de asentamiento');

            //Identificador del tipo de asentamiento llave foránea que apunta al catálogo de tipo de asentamientos
            $table->integer('tipo_asentamiento_id')->nullable()->comment('Llave foránea que relaciona con el tipo de asentamiento');
            $table->foreign('tipo_asentamiento_id')->references('id')->on('tipo_asentamientos');

            // Nombre de asentamiento
            $table->string('asentamiento')->nullable()->comment('Nombre del asentamiento');

            // Nombre del municipio o alcaldía si se trata de la CDMX
            $table->string('municipio')->nullable()->comment('Nombre del municipio a alcaldía');

            // Entidad Federativa
            $table->string('estado')->comment('Nombre de la entidad federativa');

            // Identificador de entidad federativa
            $table->char('estado_id', 2)->comment('Llave foránea que relaciona con el estado');
            $table->foreign('estado_id')->references('id')->on('estados');

            // Código postal registrado
            $table->char('cp', 5)->nullable()->comment('Código Postal');

            // Código postal determinado por georeferencia.
            $table->char('cp_por_geo', 5)->nullable()->comment('Código postal arrojado mediante geo referencia');

            // Latitud del domicilio
            $table->string('latitud')->nullable()->comment('Latitud del domicilio');

            // Longitud del domicilio
            $table->string('longitud')->nullable()->comment('Longitud del domicilio');

            // Entre calle 1
            $table->string('entre_calle1')->nullable()->comment('Primera entrecalle en la que se encuentra el domicilio');

            // Y calle 2
            $table->string('entre_calle2')->nullable()->comment('Segunda entrecalle en la que se encuentra el domicilio');

            // Referencias del domicilio, pej. Frente a gasolinera, a dos cuadras del parque, etc.
            $table->text('referencias')->nullable()->comment('Referencias del domicilio');

            // Indica si la dirección (que no el domicilio) es georeferenciable
            $table->boolean('georeferenciable')->nullable()->comment('Indica si la dirección es georeferenciable. Se refiere a dirección, no a domicilio');

            // Indica la región asignada por el algoritmo de regiones gográficas del ITAM
            $table->integer('region')->nullable()->comment('Llave foránea que relaciona con una región');

            //@ToDo: agregar la llave foránea una vez que se incluya el catálogo de cp_regiones

            $table->index(['domiciliable_id', 'domiciliable_type']);
            $table->softDeletes();
            $table->timestamps();
        });

        $tabla_nombre = 'domicilios';
        $comentario_tabla = 'Tabla donde se almacenan los domicilios de cualquier entidad domiciliable. Estructura basada en el "ACUERDO por el que aprueba la Norma Técnica sobre Domicilios Geográficos" que aparece en el DOF del 12/11/2010';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domicilios');
    }
}
