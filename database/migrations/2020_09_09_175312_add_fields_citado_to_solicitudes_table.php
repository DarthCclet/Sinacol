<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsCitadoToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->boolean('recibo_oficial')->nullable()->comment('Indica si el citado cuenta con recibo Oficial emitido por SAT');
            $table->boolean('recibo_pago')->nullable()->comment('Indica si el citado cuenta con otro tipo de recibo de pago');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn('recibo_oficial');
            $table->dropColumn('recibo_pago');
        });
    }
}
