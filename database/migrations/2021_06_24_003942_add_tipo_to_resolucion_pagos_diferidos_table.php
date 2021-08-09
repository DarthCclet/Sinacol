<?php

use App\ResolucionPagoDiferido;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoToResolucionPagosDiferidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->boolean('diferido')->nullable()->comment('Indica si se trata de un pago diferido o inmediato');
            $table->dateTime('fecha_cumplimiento')->nullable()->comment('Fecha en que se realiza el pago');
        });
        ResolucionPagoDiferido::where('audiencia_id','>','0')->update(['diferido'=>true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resolucion_pagos_diferidos', function (Blueprint $table) {
            $table->dropColumn('diferido');
            $table->dropColumn('fecha_cumplimiento');
        });
    }
}
