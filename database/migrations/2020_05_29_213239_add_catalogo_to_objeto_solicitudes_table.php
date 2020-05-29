<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCatalogoToObjetoSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/objeto_solicitudes.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $objeto_solicitud){
            $existe =  DB::table('objeto_solicitudes')->select('id')->where('id',$objeto_solicitud->id)->get();

            if(count($existe) == 0){
                    DB::table('objeto_solicitudes')->insert(
                    [
                        'id' => $objeto_solicitud->id,
                        'nombre' => $objeto_solicitud->nombre,
                        'created_at' => date("Y-m-d H:d:s"),
                        'updated_at' => date("Y-m-d H:d:s")
                        ]
                    );
            }else{
                DB::table('objeto_solicitudes')->where('id', $objeto_solicitud->id)->update(
                    [
                        'id' => $objeto_solicitud->id,
                        'nombre' => $objeto_solicitud->nombre,
                        'created_at' => date("Y-m-d H:d:s"),
                        'updated_at' => date("Y-m-d H:d:s")
                    ]
                );
            }
        }
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
