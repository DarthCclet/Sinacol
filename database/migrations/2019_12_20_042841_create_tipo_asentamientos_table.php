<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoAsentamientosTable extends Migration
{
    //Se toma la información del catálogo de tipos de asentamiento del inegi
    // https://gaia.inegi.org.mx/wscatgeo/catasentamientos

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_asentamientos', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nombre');
            $table->softDeletes()->comment('Indica la fecha y hora en que fue borrado lóigcamente un registro');
            $table->timestamps();
        });

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_asentamientos.json"));

        foreach ($json->datos as $asentamiento){

            DB::table('tipo_asentamientos')->insert(
                [
                    'id' => $asentamiento->cve_tipo_asen,
                    'nombre' => $asentamiento->descripcion
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_asentamientos');
    }
}
