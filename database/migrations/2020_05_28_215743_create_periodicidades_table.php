<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePeriodicidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodicidades', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('PK del catálogo de periodicidades');
            $table->string('nombre')->comment('Nombre de la periodicidad');
            $table->integer('dias')->nullable()->comment('Numero de dias que se van a pagar');
            $table->softDeletes()->comment('Indica la fecha y hora en que el registro se borra lógicamente.');
            $table->timestamps();
        });

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/periodicidades.json"));
        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $periodicidad){
            DB::table('periodicidades')->insert(
                [
                    'nombre' => $periodicidad->nombre,
                    'dias' => $periodicidad->dias,
                ]
            );
        }
        $tabla_nombre = 'periodicidades';
        $comentario_tabla = 'Tabla donde se almacenan periodicidades para datos laborales.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
        DB::statement('ALTER SEQUENCE periodicidades_id_seq RESTART WITH 5');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodicidades');
    }
}
