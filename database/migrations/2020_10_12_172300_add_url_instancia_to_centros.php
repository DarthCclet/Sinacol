<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;

class AddUrlInstanciaToCentros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centros', function (Blueprint $table) {
            $table->string("url_instancia_notificacion")->nullable();
        });
        $centros = Centro::all();
        foreach($centros as $centro){
            $centro->update([
                "url_instancia_notificacion" => "https://devnotifica.lxl.mx/api/v1/notificaciones"
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
        Schema::table('centros', function (Blueprint $table) {
            $table->dropColumn("url_instancia_notificacion");
        });
    }
}
