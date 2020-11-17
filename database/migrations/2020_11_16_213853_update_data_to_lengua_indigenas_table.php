<?php

use App\LenguaIndigena;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDataToLenguaIndigenasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lengua_indigenas', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/lenguas_indigenas.json"));
            //Se llena el catalogo desde el arvhivo json objeto_solicitudes.json
            foreach ($json->datos as $lengua_indigenas){
                $maxId = $lengua_indigenas->id;
                $vialidad = LenguaIndigena::find($lengua_indigenas->id);
                if($vialidad != null){
                    $vialidad->nombre = $lengua_indigenas->nombre;
                    $vialidad->save();
                }else{
                    DB::table('lengua_indigenas')->insert(
                        [
                            'id' => $lengua_indigenas->id,
                            'nombre' => $lengua_indigenas->nombre,
                            'created_at' => date("Y-m-d H:d:s"),
                            'updated_at' => date("Y-m-d H:d:s")
                        ]
                    );
                }
            }
            $lengua_indigenasExtra = LenguaIndigena::where('id','>',$maxId)->get();
            foreach ($lengua_indigenasExtra as $key => $value) {
                $value->delete();
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
        Schema::table('lengua_indigenas', function (Blueprint $table) {
            //
        });
    }
}
