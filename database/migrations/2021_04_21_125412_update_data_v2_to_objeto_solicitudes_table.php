<?php

use App\ObjetoSolicitud;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDataV2ToObjetoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('objeto_solicitudes', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/objeto_solicitudes.json"));
            //Se llena el catalogo desde el arvhivo json tipo_documentos.json
            foreach ($json->datos as $objeto){
                $tipoDoc = ObjetoSolicitud::withTrashed()->find($objeto->id);
                if($tipoDoc != null){ 
                    if($tipoDoc->deleted_at == null){
                        $tipoDoc->tipo_objeto_solicitudes_id = $objeto->tipo_objeto_solicitudes_id;
                        $tipoDoc->nombre = $objeto->nombre;
                        $tipoDoc->save();
                    }
                }else{
                    DB::table('objeto_solicitudes')->insert(
                        [
                            'id' => $objeto->id,
                            'nombre' => $objeto->nombre,
                            'tipo_objeto_solicitudes_id' => $objeto->tipo_objeto_solicitudes_id
                        ]
                    );
                }
            }
        });
        Artisan::call('cache:clear');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('objeto_solicitudes', function (Blueprint $table) {
            //
        });
    }
}
