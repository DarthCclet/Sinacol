<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClasificacionArchivosV5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clasificacion_archivos', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/clasificacion_archivos.json"));
            //Se llena el catalogo desde el arvhivo json clasificacion_archivos.json
            foreach ($json->datos as $clasificacion_archivo){
                $clasific = ClasificacionArchivo::find($clasificacion_archivo->id);
                if($clasific != null){
                    $clasific->nombre = $clasificacion_archivo->nombre;
                    $clasific->tipo_archivo_id = $clasificacion_archivo->tipo_archivo_id;
                    $clasific->entidad_emisora_id = $clasificacion_archivo->entidad_emisora_id;
                    $clasific->save();
                }else{
                    DB::table('clasificacion_archivos')->insert(
                        [
                            'nombre' => $clasificacion_archivo->nombre,
                            'tipo_archivo_id' => $clasificacion_archivo->tipo_archivo_id,
                            'entidad_emisora_id' => $clasificacion_archivo->entidad_emisora_id
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
        //
    }
}
