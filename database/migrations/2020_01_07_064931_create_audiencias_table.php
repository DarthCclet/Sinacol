<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiencias', function (Blueprint $table) {
            // llave primaria
            $table->bigIncrements('id')->comment('PK de la tabla audiencias'); 
            // id del expediente al que corresponde la audiencia
            $table->integer('expediente_id')->comment('FK de la tabla expedientes');
            $table->foreign('expediente_id')->references('id')->on('expedientes');
            // id del conciliador que da la resolución
            $table->integer('conciliador_id')->nullable()->comment('FK de la tabla conciliadores');
            $table->foreign('conciliador_id')->references('id')->on('conciliadores');
            // id de la resolución de la audiencia
            $table->integer('resolucion_id')->nullable()->comment('FK de la tabla resoluciones');
            $table->foreign('resolucion_id')->nullable()->references('id')->on('resoluciones');
            // id de la parte que sera el responsable de cumplir los acuerdos
            $table->integer('parte_responsable_id')->nullable()->comment('FK de la tabla partes');
            $table->foreign('parte_responsable_id')->nullable()->references('id')->on('partes');
            // indicador de que la audiencia se celebra junta
            $table->boolean('multiple')->nullable()->comment('indicador de que la audiencia se celebra junta');
            // fecha en que se celebrará la audiencia
            $table->date('fecha_audiencia')->comment('Fecha en la que se celebrara la audiencia');
            // hora de inicio de la audiencia
            $table->time('hora_inicio')->comment('Hora inicio en la que se celebrara la audiencia');
            // hora fin de la audiencia
            $table->time('hora_fin')->comment('Hora fin en la que se celebrara la audiencia');
            // numero consecutivo para las audiencias de un expediente
            $table->integer('numero_audiencia')->comment('Numero consecutivo de la audiencia para el expediente');
            // indicador de audiencia generada por reprogramacion
            $table->boolean('reprogramada')->comment('Indicador de Audiencia reprogramada');
            // desahgo de la resolucion
            $table->mediumText('desahogo')->nullable()->comment('Desahogo de la audiencia');
            // convenio de la resolucion
            $table->mediumText('convenio')->nullable()->comment('Convenio de la audiencia');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra logicamente.');
            $table->timestamps();
        });
        $tabla_nombre = 'audiencias';
        $comentario_tabla = 'Tabla donde se almacenan las audiencias a celebrar.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audiencias');
    }
}
