<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoNotificacionToPartes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias_partes', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_notificacion_id')->nullable()->comment('Llave foránea que relaciona con tipo_notificaciones');
            $table->date('fecha_limite_notificacion')->nullable()->comment('Indica la fecha limite para notificar');
            $table->foreign('tipo_notificacion_id')->references('id')->on('tipo_notificaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiencias_partes', function (Blueprint $table) {
            $table->dropForeign(['tipo_notificacion_id']);
            $table->dropColumn('tipo_notificacion_id');
        });
    }
}
