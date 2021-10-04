<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaAceptacionNotificacionToPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->dateTime('fecha_aceptacion_buzon')->nullable()->comment('Fecha en la que se acepta la notificacion por buzon electronico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->dropColumn('fecha_aceptacion_buzon');
        });
    }
}
