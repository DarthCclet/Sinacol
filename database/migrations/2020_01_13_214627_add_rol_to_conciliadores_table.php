<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolToConciliadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('conciliadores', function (Blueprint $table) {
          $table->unsignedBigInteger('rol_conciliador_id')->comment('FK a catálogo de rol conciliadores');
          $table->foreign('rol_conciliador_id')->references('id')->on('rol_conciliadores');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('conciliadores', function (Blueprint $table) {
          $table->dropColumn('rol_conciliador_id');
      });
    }
}
