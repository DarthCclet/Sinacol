<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoIncidenciaToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_incidencia_solicitud_id')->nullable()->comment('Indica la resolucion parte de audiencia');
            $table->foreign('tipo_incidencia_solicitud_id')->references('id')->on('tipo_incidencia_solicitudes');
            $table->unsignedBigInteger('solicitud_id')->nullable()->comment('Indica la solicitud asociada');
            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->dropColumn("incidencia_tag");
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
            $table->string('incidencia_tag')->nullable()->comment('Tag para marcar el tipo de incidencia');
            $table->dropForeign(['tipo_incidencia_solicitud_id']);
            $table->dropColumn("tipo_incidencia_solicitud_id");
            $table->dropForeign(['solicitud_id']);
            $table->dropColumn("solicitud_id");
        });
    }
}
