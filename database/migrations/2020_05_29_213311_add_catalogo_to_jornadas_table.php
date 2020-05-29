<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCatalogoToJornadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/jornadas.json"));
        //Se llena el catalogo desde el arvhivo json jornadas.json
        foreach ($json->datos as $jornadas){
           
            $existe =  DB::table('jornadas')->select('id')->where('id',$jornadas->id)->get();

            if(count($existe) == 0){
                    DB::table('jornadas')->insert(
                    [
                        'id' => $jornadas->id,
                        'nombre' => $jornadas->nombre,
                        'created_at' => date("Y-m-d H:d:s"),
                        'updated_at' => date("Y-m-d H:d:s")
                        ]
                    );
            }else{
                DB::table('jornadas')->where('id', $jornadas->id)->update(
                    [
                        'id' => $jornadas->id,
                        'nombre' => $jornadas->nombre,
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
