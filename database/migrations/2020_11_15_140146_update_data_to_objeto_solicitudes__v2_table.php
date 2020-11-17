<?php

use App\ObjetoSolicitud;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataToObjetoSolicitudesV2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('objeto_solicitudes__v2', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/objeto_solicitudes.json"));
            //Se llena el catalogo desde el arvhivo json objeto_solicitudes.json
            foreach ($json->datos as $objeto_solicitudes){
                $maxId = $objeto_solicitudes->id;
                ObjetoSolicitud::find($objeto_solicitudes->id)->update(['nombre'=>$objeto_solicitudes->nombre]);
            }
            $objeto_solicitudesExtra = ObjetoSolicitud::where('id','>',$maxId)->get();
            foreach ($objeto_solicitudesExtra as $key => $objetoSolicitud) {
                $objetoSolicitud->delete();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('objeto_solicitudes__v2', function (Blueprint $table) {
            //
        });
    }
}
