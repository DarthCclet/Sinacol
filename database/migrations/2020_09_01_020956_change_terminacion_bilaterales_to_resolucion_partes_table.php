<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTerminacionBilateralesToResolucionPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_partes', function (Blueprint $table) {
            $table->dropForeign(['resolucion_id']);
            $table->dropColumn('resolucion_id');
            $table->unsignedBigInteger('terminacion_bilateral_id')->nullable()->comment('FK de la tabla tipo_discapacidades');
            $table->foreign('terminacion_bilateral_id')->references('id')->on('terminacion_bilaterales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resolucion_partes', function (Blueprint $table) {
            $table->dropForeign(['terminacion_bilateral_id']);
            $table->dropColumn('terminacion_bilateral_id');
            $table->unsignedBigInteger('resolucion_id')->nullable()->comment('FK de la tabla tipo_discapacidades');
            $table->foreign('resolucion_id')->references('id')->on('resoluciones');
        });
    }
}
