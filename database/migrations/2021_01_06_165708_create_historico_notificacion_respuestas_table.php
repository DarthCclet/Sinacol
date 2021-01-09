<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoNotificacionRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico_notificaciones_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("historico_notificacion_id")->nullable();
            $table->foreign("historico_notificacion_id")->references("id")->on("historico_notificaciones");
            $table->unsignedBigInteger("etapa_notificacion_id")->nullable();
            $table->foreign("etapa_notificacion_id")->references("id")->on('etapas_notificaciones');
            $table->date("fecha_peticion")->nullable();
            $table->unsignedBigInteger("finalizado_id")->nullable();
            $table->string("finalizado")->nullable();
            $table->unsignedBigInteger("detalle_id")->nullable();
            $table->string("detalle")->nullable();
            $table->date("fecha_notificacion")->nullable();
            $table->unsignedBigInteger("documento_id")->nullable();
            $table->foreign("documento_id")->references("id")->on("documentos");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historico_notificaciones_respuestas');
    }
}
