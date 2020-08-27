<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDatosLaboralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->string('nombre_contrato')->nullable()->comment('Nombre de quien te contrato');
            $table->string('nombre_paga')->nullable()->comment('Nombre de quien te paga');
            $table->string('nombre_prestas_servicio')->nullable()->comment('Nombre de a quien le prestas servicio ');
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
            $table->dropColumn('nombre_contrato');
            $table->dropColumn('nombre_paga');
            $table->dropColumn('nombre_prestas_servicio');
        });
    }
}
