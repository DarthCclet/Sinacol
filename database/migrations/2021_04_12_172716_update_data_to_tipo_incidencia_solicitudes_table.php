<?php

use App\TipoIncidenciaSolicitud;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDataToTipoIncidenciaSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_incidencia_solicitudes.json"));
        //Se llena el catalogo desde el arvhivo json tipo_documentos.json
        foreach ($json->datos as $objeto){
            $tipoDoc = TipoIncidenciaSolicitud::find($objeto->id);
            if($tipoDoc != null){
                $tipoDoc->update(['nombre'=>$objeto->nombre,
                                'descripcion' => $objeto->descripcion
                ]);
            }else{
                DB::table('tipo_incidencia_solicitudes')->insert(
                    [
                        'nombre' => $objeto->nombre,
                        'descripcion' => $objeto->descripcion
                    ]
                );
            }
        }
        Artisan::call('cache:clear');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
