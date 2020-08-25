<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoToClasificacionArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clasificacion_archivos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_archivo_id')->nullable()->comment('Llave foránea que relaciona con el tipo archivo');
            $table->foreign('tipo_archivo_id')->references('id')->on('etapa_resoluciones');
            $table->unsignedBigInteger('entidad_emisora_id')->nullable()->comment('Llave foránea que relaciona con la entidad que emite el documento');
            $table->foreign('entidad_emisora_id')->references('id')->on('entidades_emisoras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clasificacion_archivos', function (Blueprint $table) {
            //
        });
    }
}
