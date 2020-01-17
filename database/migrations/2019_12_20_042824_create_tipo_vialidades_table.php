<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoVialidadesTable extends Migration
{
    //Se toma del web service del inegi
    // https://gaia.inegi.org.mx/wscatgeo/catavialidad

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_vialidades', function (Blueprint $table) {
            $table->integer('id')->primary()->comment('Llave primaria del registro');
            $table->string('nombre')->comment('Nombre del tipo de vialidad');
            $table->timestamps();
        });

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_vialidades.json"));

        foreach ($json->datos as $vialidad){

            DB::table('tipo_vialidades')->insert(
                [
                    'id' => $vialidad->cve_tipo_vial,
                    'nombre' => $vialidad->descripcion
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
        Schema::dropIfExists('tipo_vialidades');
    }
}
