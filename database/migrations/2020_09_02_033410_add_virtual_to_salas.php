<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;
use App\Sala;

class AddVirtualToSalas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salas', function (Blueprint $table) {
            $table->boolean('virtual')->default(false)->nullable()->comment("Indicador de que es una sala virtual");
        });
        $centros = Centro::all();
        foreach($centros as $centro){
            $sala = Sala::create([
                "sala" => $centro->abreviatura."-virtual",
                "centro_id" => $centro->id,
                "virtual" => true
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salas', function (Blueprint $table) {
            $table->dropColumn('virtual');
        });
    }
}
