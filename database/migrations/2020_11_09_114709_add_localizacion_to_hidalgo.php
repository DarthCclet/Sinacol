<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;

class AddLocalizacionToHidalgo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $centroIds = [
            ["centro_id" => 13,"latitud" => "20.1090652","longitud" => "-98.7470731"]
        ];
        foreach($centroIds as $centros){
            $centro = Centro::find($centros["centro_id"]);
            if($centro != null){
                if($centro->domicilio ==null){
                    $centro->domicilio()->create([
                        'tipo_vialidad' => "CERRADA",
                        'tipo_vialidad_id' => "8",
                        'vialidad' => "Everardo Márquez",
                        'num_ext' => "115",
                        'asentamiento' => "Ex Hacienda de Coscotitlán",
                        'municipio' => "PACHUCA DE SOTO",
                        'estado' => "Hidalgo",
                        'estado_id' => "13",
                        'cp' => "42086",
                        'latitud' => $centros["latitud"],
                        'longitud' => $centros["longitud"]
                    ]);
                }else{
                    var_dump("holi");
                    $centro->domicilio()->update([
                        'tipo_vialidad' => "CERRADA",
                        'tipo_vialidad_id' => "8",
                        'vialidad' => "Everardo Márquez",
                        'num_ext' => "115",
                        'asentamiento' => "Ex Hacienda de Coscotitlán",
                        'municipio' => "PACHUCA DE SOTO",
                        'estado' => "Hidalgo",
                        'estado_id' => "13",
                        'cp' => "42086",
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
        $centro = Centro::find(13);
        $centro->domicilio()->update([
            "latitud" => null,
            "longitud" => null
        ]);
    }
}
