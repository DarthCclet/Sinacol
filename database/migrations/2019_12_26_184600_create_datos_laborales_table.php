<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatosLaboralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datos_laborales', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de datos laborales de la parte');
            $table->string('nombre_jefe_directo')->comment('Nombre del jefe directo');
            // $table->unsignedBigInteger('puesto_id')->comment('FK del puesto ocupado');
            $table->string('puesto')->comment('Nombre del puesto laboral');
            $table->integer('nss')->comment('Número de seguro social');
            $table->integer('no_issste')->comment('Número de issste');
            $table->integer('no_afore')->comment('Número de afore');
            $table->decimal('percepcion_mensual_neta', 10, 2)->comment('Monto de percepción mensual neta');
            $table->decimal('percepcion_mensual_bruta', 10, 2)->comment('Monto de percepción mensual bruta');
            $table->boolean('labora_actualmente')->comment('Indica si la parte labora actualmente');
            $table->dateTime('fecha_ingreso', 0)->comment('Fecha de ingreso del trabajador');
            $table->dateTime('fecha_salida', 0)->comment('Fecha de salida del trabajador');
            $table->unsignedBigInteger('jornada_id')->comment('FK de tipo de jornada laboral de la parte');
            $table->integer('horas_semanales')->comment('Número de horas laboradas semanalmente');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();

            $table->foreign('jornada_id')->references('id')->on('jornadas');
        });
        $tabla_nombre = 'datos_laborales';
        $comentario_tabla = 'Tabla donde se almacenan los registros de los datos laborales de las partes.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datos_laborales');
    }
}
