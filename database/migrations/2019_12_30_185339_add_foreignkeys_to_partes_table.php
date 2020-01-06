<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeysToPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes', function (Blueprint $table) {
            $table->foreign('solicitud_id')->references('id')->on('solicitudes');
            $table->foreign('tipo_parte_id')->references('id')->on('tipo_partes');
            $table->foreign('tipo_persona_id')->references('id')->on('tipo_personas');
            $table->foreign('nacionalidad_id')->references('id')->on('nacionalidades');
            $table->foreign('entidad_nacimiento_id')->references('id')->on('estados');
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
          $table->dropForeign(['solicitud_id']);
          $table->dropForeign(['tipo_parte_id']);
          $table->dropForeign(['tipo_persona_id']);
          $table->dropForeign(['nacionalidad_id']);
          $table->dropForeign(['entidad_nacimiento_id']);
        });
    }
}
