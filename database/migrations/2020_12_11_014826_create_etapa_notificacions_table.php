<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\EtapaNotificacion;

class CreateEtapaNotificacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etapas_notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('etapa');
            $table->softDeletes();
            $table->timestamps();
        });
        collect([
            ["etapa" => "Ratificación"],
            ["etapa" => "Cambio de Fecha"],
            ["etapa" => "No comparecio el citado"],
            ["etapa" => "Multa"]
        ])->each(function ($items){
            EtapaNotificacion::create($items);
        });
        Schema::table('audiencias', function (Blueprint $table) {
            $table->unsignedBigInteger('etapa_notificacion_id')->nullable();
            $table->foreign('etapa_notificacion_id')->references('id')->on('etapas_notificaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etapas_notificaciones');
        Schema::table('audiencias', function (Blueprint $table) {
            $table->dropColumn('etapa_notificacion_id');
        });
    }
}
