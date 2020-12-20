<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncidenciasToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->boolean('incidencia')->nullable()->comment('Bool para regsitrar si hay Incidencia en la solicitud ');
            $table->string('incidencia_tag')->nullable()->comment('Tag para marcar el tipo de incidencia');
            $table->text('justificacion_incidencia')->nullable();
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
            $table->dropColumn("incidencia");
            $table->dropColumn("incidencia_tags");
            $table->dropColumn("justificacion_incidencia");
        });
    }
}
