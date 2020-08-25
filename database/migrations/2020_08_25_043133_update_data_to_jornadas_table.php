<?php

use App\Jornada;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataToJornadasTable extends Migration
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
            $maxId = $jornadas->id;
            Jornada::find($jornadas->id)->update(['nombre'=>$jornadas->nombre]);
        }
        $jornadasExtra = Jornada::where('id','>',$maxId)->get();
        foreach ($jornadasExtra as $key => $jornada) {
            $jornada->delete();
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
        Artisan::call('cache:clear');
    }
}
