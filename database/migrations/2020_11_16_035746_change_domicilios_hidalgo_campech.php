<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;

class ChangeDomiciliosHidalgoCampech extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $centroIds = [
            [
                "centro_id" => 10,
                "tipo_vialidad" => "CALLE",
                "tipo_vialidad_id" =>"5",
                "vialidad" =>"Elorreaga",
                "num_ext" => "262",
                "num_int" => null,
                'asentamiento' => "CENTRO",
                'municipio' => "Durango",
                'estado' => "Durango",
                'estado_id' => "10",
                'cp' => "34000",
                'latitud' => "24.032333",
                'longitud' => "-104.668724"
            ],
            [
                "centro_id" => 4,
                "tipo_vialidad" => "AVENIDA",
                "tipo_vialidad_id" =>"3",
                "vialidad" =>"Isla de Tris",
                "num_ext" => "57",
                "num_int" => "letra G, locales 203, 204 y 205",
                'asentamiento' => "Col. El tambor II",
                'municipio' => "Ciudad del Carmen",
                'estado' => "Campeche",
                'estado_id' => "04",
                'cp' => "24100",
                'latitud' => "18.655596",
                'longitud' => "-91.789200"
            ],
        ];
        foreach($centroIds as $centros){
            $centro = Centro::find($centros["centro_id"]);
            if($centro != null){
                if($centro->domicilio ==null){
                    $centro->domicilio()->create([
                        'tipo_vialidad' => $centros["tipo_vialidad"],
                        'tipo_vialidad_id' => $centros["tipo_vialidad_id"],
                        'vialidad' => $centros["vialidad"],
                        'num_ext' => $centros["num_ext"],
                        'asentamiento' => $centros["asentamiento"],
                        'municipio' => $centros["municipio"],
                        'estado' => $centros["estado"],
                        'estado_id' => $centros["estado_id"],
                        'cp' => $centros["cp"],
                        'latitud' => $centros["latitud"],
                        'longitud' => $centros["longitud"]
                    ]);
                }else{
                    $centro->domicilio()->update([
                        'tipo_vialidad' => $centros["tipo_vialidad"],
                        'tipo_vialidad_id' => $centros["tipo_vialidad_id"],
                        'vialidad' => $centros["vialidad"],
                        'num_ext' => $centros["num_ext"],
                        'asentamiento' => $centros["asentamiento"],
                        'municipio' => $centros["municipio"],
                        'estado' => $centros["estado"],
                        'estado_id' => $centros["estado_id"],
                        'cp' => $centros["cp"],
                        'latitud' => $centros["latitud"],
                        'longitud' => $centros["longitud"]
                    ]);
                }
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
        //
    }
}
