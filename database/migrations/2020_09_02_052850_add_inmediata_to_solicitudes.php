<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInmediataToSolicitudes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->boolean("inmediata")->default(true)->nullable();
        });
        collect([
            ['nombre'=>'Conciliador en sala'],
            ['nombre'=>'Conciliador de previo acuerdo'],
        ])->each(function ($item){
            \App\RolAtencion::create($item);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn("inmediata");
        });
    }
}
