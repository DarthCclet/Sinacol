<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoArchivosToClasificacionArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clasificacion_archivos', function (Blueprint $table) {
            $table->dropForeign('tipo_archivo_id');
            $table->dropColumn('tipo_archivo_id');
            $table->unsignedBigInteger('tipo_archivo_id')->nullable()->comment('Llave foránea que relaciona con el tipo archivo');
            $table->foreign('tipo_archivo_id')->references('id')->on('tipo_archivos');

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
            $table->dropColumn('tipo_archivo_id');
            $table->dropForeign('tipo_archivo_id')->references('id')->on('tipo_archivos');
        });
    }
}
