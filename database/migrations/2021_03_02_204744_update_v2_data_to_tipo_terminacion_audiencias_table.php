<?php

use App\TipoTerminacionAudiencia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateV2DataToTipoTerminacionAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_terminacion_audiencias', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/tipo_terminacion_audiencias.json"));
            //Se llena el catalogo desde el arvhivo json tipo_terminacion_audienciass.json
            foreach ($json->datos as $tipo_terminacion_audiencia){
                $clasific = TipoTerminacionAudiencia::find($tipo_terminacion_audiencia->id);
                if($clasific != null){
                    $clasific->nombre = $tipo_terminacion_audiencia->nombre;
                    $clasific->descripcion = $tipo_terminacion_audiencia->descripcion;
                    $clasific->save();
                }else{
                    DB::table('tipo_terminacion_audiencias')->insert(
                        [
                            'nombre' => $tipo_terminacion_audiencia->nombre,
                            'descripcion' => $tipo_terminacion_audiencia->descripcion
                        ]
                    );
                }
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
        Schema::table('tipo_terminacion_audiencias', function (Blueprint $table) {
            //
        });
    }
}
