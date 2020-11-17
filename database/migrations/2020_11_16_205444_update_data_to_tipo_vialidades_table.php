<?php

use App\TipoVialidad;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDataToTipoVialidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_vialidades', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/tipo_vialidades.json"));
            //Se llena el catalogo desde el arvhivo json objeto_solicitudes.json
            foreach ($json->datos as $tipo_vialidades){
                $maxId = $tipo_vialidades->cve_tipo_vial;
                $vialidad = TipoVialidad::find($tipo_vialidades->cve_tipo_vial);
                if($vialidad != null){
                    $vialidad->nombre = $tipo_vialidades->descripcion;
                    $vialidad->save();
                }else{
                    DB::table('tipo_vialidades')->insert(
                        [
                            'id' => $tipo_vialidades->cve_tipo_vial,
                            'nombre' => $tipo_vialidades->descripcion,
                            'created_at' => date("Y-m-d H:d:s"),
                            'updated_at' => date("Y-m-d H:d:s")
                        ]
                    );
                }
            }
            $tipo_vialidadesExtra = TipoVialidad::where('id','>',$maxId)->get();
            foreach ($tipo_vialidadesExtra as $key => $objetoSolicitud) {
                $objetoSolicitud->delete();
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
        Schema::table('tipo_vialidades', function (Blueprint $table) {
            //
        });
    }
}
