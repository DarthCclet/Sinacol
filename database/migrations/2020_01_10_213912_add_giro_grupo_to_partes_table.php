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
          
          $table->unsignedBigInteger('grupo_prioritario_id')->nullable()->comment('FK a catálogo de gruos prioritarios');
          $table->foreign('grupo_prioritario_id')->references('id')->on('grupos_prioritarios');
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
          $table->dropColumn('grupo_prioritario_id');
      });
    }
}
