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
            $table->bigIncrements('id');
            $table->string('nombre_jefe_directo');
            $table->string('puesto');
            $table->integer('nss');
            $table->integer('no_issste');
            $table->integer('no_afore');
            $table->decimal('percepcion_mensual_neta', 10, 2);
            $table->decimal('percepcion_mensual_bruta', 10, 2);
            $table->boolean('labora_actualmente');
            $table->dateTime('fecha_ingreso', 0);
            $table->dateTime('fecha_salida', 0);
            $table->unsignedBigInteger('jornada_id');
            $table->integer('horas_semanales');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('jornada_id')->references('id')->on('jornadas');

        });
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
