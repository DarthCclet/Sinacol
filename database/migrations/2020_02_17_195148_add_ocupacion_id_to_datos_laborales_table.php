<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOcupacionIdToDatosLaboralesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datos_laborales', function (Blueprint $table) {
            $table->unsignedBigInteger('ocupacion_id')->nullable()->comment('FK a catálogo ocupaciones');
            $table->foreign('ocupacion_id')->references('id')->on('ocupaciones');
            $table->dropColumn('puesto');
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
            $table->dropColumn('ocupacion_id');
            $table->string('puesto')->comment('Nombre del puesto laboral');
        });
    }
}
