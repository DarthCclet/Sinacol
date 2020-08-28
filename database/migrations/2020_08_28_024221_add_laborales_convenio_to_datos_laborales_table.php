<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLaboralesConvenioToDatosLaboralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->string('horario_laboral')->nullable()->comment('horario laboral');
            $table->string('horario_comida')->nullable()->comment('horario de comida');
            $table->boolean('comida_dentro')->default(false)->comment('Indica si la comida la toma dentro de las instalaciones laborales');
            $table->string('dias_descanso')->nullable()->comment('total de dias de descanso y especificacion de dias');
            $table->string('dias_vacaciones')->nullable()->comment('total de dias de vacaciones');
            $table->string('dias_aguinaldo')->nullable()->comment('total de dias de aguinaldo');
            $table->string('prestaciones_adicionales')->nullable()->comment('prestaciones adicionales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->dropColumn('horario_laboral');
            $table->dropColumn('horario_comida');
            $table->dropColumn('comida_dentro');
            $table->dropColumn('dias_descanso');
            $table->dropColumn('dias_vacaciones');
            $table->dropColumn('dias_aguinaldo');
            $table->dropColumn('prestaciones_adicionales');
        });
    }
}
