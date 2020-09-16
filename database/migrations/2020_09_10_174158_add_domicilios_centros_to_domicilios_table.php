<?php

use App\Centro;
use App\Domicilio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDomiciliosCentrosToDomiciliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/domicilio_centros.json"));
            //Se llena el catalogo desde el archivo json domicilio_centros.json
            $domicilios = Domicilio::where('domiciliable_type','App\Centro')->get();
            if(count($domicilios) > 0){ //si hay domicilios por actualizar
                foreach ($json->datos as $objeto){
                    $centro = Centro::find($objeto->domiciliable_id);
                    if($centro != null){
                        if($centro->domicilio ==null){
                            $centro->domicilio()->create(
                                [
                                // 'domiciliable_id' => $objeto->domiciliable_id,
                                // 'domiciliable_type' => $objeto->domiciliable_type,
                                'tipo_vialidad' => $objeto->tipo_vialidad,
                                'tipo_vialidad_id' => $objeto->tipo_vialidad_id,
                                'vialidad' => $objeto->vialidad,
                                'num_ext' => $objeto->num_ext,
                                'num_int' => $objeto->num_int,
                                'asentamiento' => $objeto->asentamiento,
                                'municipio' => $objeto->municipio,
                                'estado' => $objeto->estado,
                                'estado_id' => $objeto->estado_id,
                                'cp' => $objeto->cp,
                                'referencias' => $objeto->referencias,
                                'region' => $objeto->region,
                                'hora_atencion_de' => $objeto->hora_atencion_de,
                                'hora_atencion_a' => $objeto->hora_atencion_a
                                ]
                            );
                        }else{
                            $domicilio = Domicilio::where('domiciliable_type','App\Centro')->where('domiciliable_id',$centro->id)->first();
                            if($domicilio != null){
                                $domicilio->update([
                                    // 'domiciliable_id' => $objeto->domiciliable_id,
                                    // 'domiciliable_type' => $objeto->domiciliable_type,
                                    'tipo_vialidad' => $objeto->tipo_vialidad,
                                    'tipo_vialidad_id' => $objeto->tipo_vialidad_id,
                                    'vialidad' => $objeto->vialidad,
                                    'num_ext' => $objeto->num_ext,
                                    'num_int' => $objeto->num_int,
                                    'asentamiento' => $objeto->asentamiento,
                                    'municipio' => $objeto->municipio,
                                    'estado' => $objeto->estado,
                                    'estado_id' => $objeto->estado_id,
                                    'cp' => $objeto->cp,
                                    'referencias' => $objeto->referencias,
                                    'region' => $objeto->region,
                                    'hora_atencion_de' => $objeto->hora_atencion_de,
                                    'hora_atencion_a' => $objeto->hora_atencion_a
                                ]);
                            }
                        }
                    }
                }
            }else{ //si no hay domicilios a actualizar
                foreach ($json->datos as $objeto){
                    DB::table('domicilios')->insert(
                    [
                        'domiciliable_id' => $objeto->domiciliable_id,
                        'domiciliable_type' => $objeto->domiciliable_type,
                        'tipo_vialidad' => $objeto->tipo_vialidad,
                        'tipo_vialidad_id' => $objeto->tipo_vialidad_id,
                        'vialidad' => $objeto->vialidad,
                        'num_ext' => $objeto->num_ext,
                        'num_int' => $objeto->num_int,
                        'asentamiento' => $objeto->asentamiento,
                        'municipio' => $objeto->municipio,
                        'estado' => $objeto->estado,
                        'estado_id' => $objeto->estado_id,
                        'cp' => $objeto->cp,
                        'referencias' => $objeto->referencias,
                        'region' => $objeto->region,
                        'hora_atencion_de' => $objeto->hora_atencion_de,
                        'hora_atencion_a' => $objeto->hora_atencion_a
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
        Schema::table('domicilios', function (Blueprint $table) {
            //
        });
    }
}
