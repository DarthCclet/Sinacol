<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDataToSalariosMinimosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('salarios_minimos')->insert(
            [
                'salario_minimo' => 123.22,
                'salario_minimo_zona_libre' => 185.56
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salarios_minimos', function (Blueprint $table) {
            //
        });
    }
}
