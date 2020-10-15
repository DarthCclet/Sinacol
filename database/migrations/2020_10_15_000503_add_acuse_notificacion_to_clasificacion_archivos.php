<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\ClasificacionArchivo;

class AddAcuseNotificacionToClasificacionArchivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ClasificacionArchivo::create([
            "nombre" => "Notificacion",
            "tipo_documento_id" => 4
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $clasificacion = ClasificacionArchivo::where("nombre","Notificacion")->first();
        $clasificacion->delete();
    }
}
