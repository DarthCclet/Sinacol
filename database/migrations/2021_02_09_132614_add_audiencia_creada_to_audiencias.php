<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAudienciaCreadaToAudiencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->boolean("audiencia_creada")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiencias', function (Blueprint $table) {
            $table->dropColumn("audiencia_creada");
        });
    }
}
