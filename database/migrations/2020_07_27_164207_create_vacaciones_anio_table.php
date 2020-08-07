<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVacacionesAnioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacaciones_anios', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK de la tabla tipo_contadores');
            $table->integer('anios_laborados')->comment('Numero de anios laborados');
            $table->integer('dias_vacaciones')->comment('Numero de dias de vacaciones');
            $table->string('descripcion')->comment('Descripcion de vacaciones por anios laborados');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro, modifica y se borra lógicamente.');
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/vacaciones_anio.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $dias_vacaciones){
            DB::table('vacaciones_anios')->insert(
                [
                    'id' => $dias_vacaciones->id,
                    'anios_laborados' => $dias_vacaciones->anios_laborados,
                    'dias_vacaciones' => $dias_vacaciones->dias_vacaciones,
                    'descripcion' => $dias_vacaciones->descripcion
                ]
            );
        }
        $tabla_nombre = 'vacaciones_anios';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de los dias de vacaciones por anio laborado.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacaciones_anio');
    }
}
