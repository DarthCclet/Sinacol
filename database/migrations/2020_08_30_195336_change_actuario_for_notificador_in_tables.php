<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeActuarioForNotificadorInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $sql = "update tipo_notificaciones set nombre = replace(nombre, 'actuario', 'notificador')";
            $sql1 = "update tipo_notificaciones set nombre = replace(nombre, 'solicitado', 'citaado')";

            DB::statement($sql);
            DB::statement($sql1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $sql = "update tipo_notificaciones set nombre = replace(nombre, 'notificador', 'actuario')";
            $sql1 = "update tipo_notificaciones set nombre = replace(nombre, 'citado', 'solicitado')";

            DB::statement($sql);
            DB::statement($sql1);

        });
    }
}
