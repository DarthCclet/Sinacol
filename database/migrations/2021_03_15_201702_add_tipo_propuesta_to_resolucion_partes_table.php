<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPropuestaToResolucionPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_partes', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_propuesta_pago_id')->nullable()->comment('FK a catálogo tipo propuesta pagos');
            $table->foreign('tipo_propuesta_pago_id')->references('id')->on('tipo_propuesta_pagos');
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
            $table->dropForeign('tipo_propuesta_pago_id');
            $table->dropColumn('tipo_propuesta_pago_id');
        });
    }
}
