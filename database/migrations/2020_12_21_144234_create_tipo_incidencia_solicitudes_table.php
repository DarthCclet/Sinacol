<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoIncidenciaSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_incidencia_solicitudes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->comment('Nombre del tipo de la incidencia  ');
            $table->text('descripcion')->comment('descripcion del tipo de la incidencia  ');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/tipo_incidencia_solicitudes.json"));
        foreach ($json->datos as $tipo_incidencia_solicitudes){
            DB::table('tipo_incidencia_solicitudes')->insert(
                [
                    'nombre' => $tipo_incidencia_solicitudes->nombre,
                    'descripcion' => $tipo_incidencia_solicitudes->descripcion,
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
        Schema::dropIfExists('tipo_incidencia_solicitudes');
    }
}
