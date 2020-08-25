<?php

use App\EtapaResolucion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataToEtapaResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/etapa_resoluciones.json"));
        //Se llena el catalogo desde el arvhivo json etapa_resoluciones.json
        foreach ($json->datos as $objeto){
            $maxId = $objeto->id;
            EtapaResolucion::find($objeto->id)->update(['nombre'=>$objeto->nombre]);
        }
        $etapa_resoluciones = EtapaResolucion::where('id','>',$maxId)->get();
        foreach ($etapa_resoluciones as $key => $objeto) {
            $objeto->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etapa_resoluciones', function (Blueprint $table) {
            //
        });
    }
}
