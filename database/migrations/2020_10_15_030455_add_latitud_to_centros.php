<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Centro;

class AddLatitudToCentros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $centroIds = [
            ["centro_id" => 4,"latitud" => "18.645691","longitud" => "-91.80231"],
            ["centro_id" => 10,"latitud" => "24.0340395","longitud" => "-104.6360597"],
            ["centro_id" => 24,"latitud" => "22.1538825","longitud" => "-100.9781662"],
            ["centro_id" => 15,"latitud" => "19.3137542","longitud" => "-99.6386443"],
            ["centro_id" => 7,"latitud" => "16.7564742","longitud" => "-93.1427455"],
            ["centro_id" => 27,"latitud" => "17.9982799","longitud" => "-92.9224995"],
            ["centro_id" => 32,"latitud" => "22.760176","longitud" => "-102.546866"],
            ["centro_id" => 13,"latitud" => "","longitud" => ""]
        ];
        foreach($centroIds as $centros){
            $centro = Centro::find($centros["centro_id"]);
            if($centro != null){
                $centro->domicilio()->update([
                    "latitud" => $centros["latitud"],
                    "longitud" => $centros["longitud"]
                ]);
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
        $centroIds = [
            ["centro_id" => 4,"latitud" => "18.645691","longitud" => "-91.80231"],
            ["centro_id" => 10,"latitud" => "24.0340395","longitud" => "-104.6360597"],
            ["centro_id" => 24,"latitud" => "22.1538825","longitud" => "-100.9781662"],
            ["centro_id" => 15,"latitud" => "19.3137542","longitud" => "-99.6386443"],
            ["centro_id" => 7,"latitud" => "16.7564742","longitud" => "-93.1427455"],
            ["centro_id" => 27,"latitud" => "17.9982799","longitud" => "-92.9224995"],
            ["centro_id" => 32,"latitud" => "22.760176","longitud" => "-102.546866"],
            ["centro_id" => 13,"latitud" => "","longitud" => ""]
        ];
        foreach($centroIds as $centros){
            $centro = Centro::find($centros["centro_id"]);
            if($centro != null){
                $centro->domicilio()->update([
                    "latitud" => null,
                    "longitud" => null
                ]);
            }
        }
    }
}
