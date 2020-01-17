<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->char('id',2)->primary();
            $table->string('nombre');
            $table->timestamps();
        });

        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/estados.json"));

        foreach ($json->datos as $estado){
            DB::table('estados')->insert(
                [
                    'id' => $estado->cve_agee,
                    'nombre' => $estado->nom_agee
                ]
            );
        }

        $tabla_nombre = 'estados';
        $comentario_tabla = 'Tabla donde se almacenan el catalogo de Estados.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estados');
    }
}
