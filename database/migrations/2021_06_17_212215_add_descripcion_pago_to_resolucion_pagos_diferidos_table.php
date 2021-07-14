<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescripcionPagoToResolucionPagosDiferidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->string('descripcion_pago')->nullable()->comment('Descripcion del pago en especie o reconocimiento de derechos');
            $table->decimal('monto', 10, 2)->nullable()->change();
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
            $table->dropColumn('descripcion_pago');
        });
    }
}
