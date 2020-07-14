<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConceptoPagoResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepto_pago_resoluciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->softDeletes();
            $table->timestamps();
        });
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/concepto_pago_resoluciones.json"));

        //Se llena el catalogo desde el arvhivo json generos.json
        foreach ($json->datos as $concepto_pago_resoluciones){
            DB::table('concepto_pago_resoluciones')->insert(
                [
                    'id' => $concepto_pago_resoluciones->id,
                    'nombre' => $concepto_pago_resoluciones->nombre,
                    'created_at' => date("Y-m-d H:d:s"),
                    'updated_at' => date("Y-m-d H:d:s")
                ]
            );
        }

        $tabla_nombre = 'concepto_pago_resoluciones';
        $comentario_tabla = 'Tabla donde se almacena el catalogo de conceptos de pago resoluciones.';
        DB::statement("COMMENT ON TABLE $tabla_nombre IS '$comentario_tabla'");
        DB::statement('ALTER SEQUENCE concepto_pago_resoluciones_id_seq RESTART WITH 4');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concepto_pago_resoluciones');
    }
}
