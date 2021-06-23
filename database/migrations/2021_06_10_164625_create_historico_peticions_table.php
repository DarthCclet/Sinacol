<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoPeticionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('historico_notificaciones_respuestas', 'historico_notificaciones_respuestas_anterior');
        Schema::create('historico_notificaciones_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("finalizado_id")->nullable();
            $table->string("finalizado")->nullable();
            $table->unsignedBigInteger("detalle_id")->nullable();
            $table->string("detalle")->nullable();
            $table->date('fecha_notificacion');
            $table->unsignedBigInteger("documento_id")->nullable();
            $table->foreign("documento_id")->references("id")->on("documentos");
            $table->unsignedBigInteger('historico_notificacion_id')->nullable();
            $table->foreign('historico_notificacion_id')->references('id')->on('historico_notificaciones');
            $table->timestamps();
        });
        Schema::create('historico_notificaciones_peticiones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etapa_notificacion_id');
            $table->foreign('etapa_notificacion_id')->references('id')->on('etapas_notificaciones');
            $table->dateTime('fecha_peticion_notificacion');
            $table->unsignedBigInteger('historico_notificacion_respuesta_id')->nullable();
            $table->foreign('historico_notificacion_respuesta_id')->references('id')->on('historico_notificaciones_respuestas');
            $table->unsignedBigInteger('historico_notificacion_id')->nullable();
            $table->foreign('historico_notificacion_id')->references('id')->on('historico_notificaciones');
            $table->timestamps();
        });
        Schema::table('historico_notificaciones',function (Blueprint $table){
            $table->unsignedBigInteger('historico_notificacion_peticion_id')->nullable();
            $table->foreign('historico_notificacion_peticion_id')->references('id')->on('historico_notificaciones_peticiones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico_notificaciones',function (Blueprint $table){
            $table->dropColumn('historico_notificacion_peticion_id');
        });
        Schema::dropIfExists('historico_notificaciones_peticiones');
        Schema::dropIfExists('historico_notificaciones_respuestas');
        Schema::rename('historico_notificaciones_respuestas_anterior', 'historico_notificaciones_respuestas');
    }
}
