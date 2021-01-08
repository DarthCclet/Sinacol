<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTipoTerminacionAudienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_terminacion_audiencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->comment('Nombre del tipo de terminacion de audiencia  ');
            $table->text('descripcion')->comment('descripcion del tipo de terminacion de la audiencia  ');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_terminacion_audiencias.json"));
        foreach ($json->datos as $tipo_terminacion_audiencias){
            DB::table('tipo_terminacion_audiencias')->insert(
                [
                    'nombre' => $tipo_terminacion_audiencias->nombre,
                    'descripcion' => $tipo_terminacion_audiencias->descripcion,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
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
        Schema::dropIfExists('tipo_terminacion_audiencias');
    }
}
