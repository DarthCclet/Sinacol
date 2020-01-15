<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGiroGrupoToPartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('partes', function (Blueprint $table) {
          //
          $table->unsignedBigInteger('giro_comercial_id')->comment('FK a catálogo de giros comerciales');
          $table->foreign('giro_comercial_id')->references('id')->on('giro_comerciales');
          $table->unsignedBigInteger('grupo_vulnerable_id')->comment('FK a catálogo de gruos vulnerables');
          $table->foreign('grupo_vulnerable_id')->references('id')->on('grupos_vulnerables');
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
          //
          $table->dropColumn('giro_comercial_id');
          $table->dropColumn('grupo_vulnerable_id');
      });
    }
}
