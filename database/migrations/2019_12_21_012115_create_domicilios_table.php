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

            $table->bigIncrements('id');

            // LLave foranea que apunta al objeto que se asigna el domicilio
            $table->bigInteger('domiciliable_id');

            // Clase del objeto al que se está asignando el domicilio
            $table->string('domiciliable_type');

            // Tipo de vialidad
            $table->string('tipo_vialidad');

            // Id del tipo de vialidad en el catálogo de tipos de vialidades
            $table->integer('tipo_vialidad_id');
            $table->foreign('tipo_vialidad_id')->references('id')->on('tipo_vialidades');

            // Nombre de la vialidad (o calle)
            $table->string('vialidad');

            // Número exterior
            $table->string('num_ext');

            // Número interior
            $table->string('num_int')->nullable();

            // Tipo de asentamiento (del catálogo de tipos de asentamiento)
            $table->string('tipo_asentamiento')->nullable();

            //Identificador del tipo de asentamiento llave foránea que apunta al catálogo de tipo de asentamientos
            $table->integer('tipo_asentamiento_id')->nullable();
            $table->foreign('tipo_asentamiento_id')->references('id')->on('tipo_asentamientos');

            // Nombre de asentamiento
            $table->string('asentamiento')->nullable();

            // Nombre del municipio o alcaldía si se trata de la CDMX
            $table->string('municipio')->nullable();

            // Entidad Federativa
            $table->string('estado');

            // Identificador de entidad federativa
            $table->char('estado_id', 2);
            $table->foreign('estado_id')->references('id')->on('estados');

            // Código postal registrado
            $table->char('cp', 5)->nullable();

            // Código postal determinado por georeferencia.
            $table->char('cp_por_geo', 5)->nullable();

            // Latitud del domicilio
            $table->string('latitud')->nullable();

            // Longitud del domicilio
            $table->string('longitud')->nullable();

            // Entre calle 1
            $table->string('entre_calle1')->nullable();

            // Y calle 2
            $table->string('entre_calle2')->nullable();

            // Referencias del domicilio, pej. Frente a gasolinera, a dos cuadras del parque, etc.
            $table->text('referencias')->nullable();

            // Indica si la dirección (que no el domicilio) es georeferenciable
            $table->boolean('georeferenciable')->nullable();

            // Indica la región asignada por el algoritmo de regiones gográficas del ITAM
            $table->integer('region')->nullable();

            $table->index(['domiciliable_id', 'domiciliable_type']);
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
        Schema::dropIfExists('domicilios');
    }
}
