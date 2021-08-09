<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInformacionPagoToResolucionPagosDiferidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->text('informacion_pago')->change();
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
            $table->string('informacion_pago')->change();
        });
    }
}
