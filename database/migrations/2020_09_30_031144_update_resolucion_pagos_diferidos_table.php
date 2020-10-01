<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateResolucionPagosDiferidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->dropForeign(['resolucion_parte_id']);
            $table->dropColumn('resolucion_parte_id');
            $table->boolean('pagado')->nullable()->change();
            
            // id de la audiencia 
            $table->integer('audiencia_id')->comment('FK de la tabla audiencias');
            $table->foreign('audiencia_id')->references('id')->on('audiencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->dropForeign('audiencia_id');
            $table->dropColumn('audiencia_id');
        });
    }
}
