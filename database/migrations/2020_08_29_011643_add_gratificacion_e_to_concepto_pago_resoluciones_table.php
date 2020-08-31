<?php

use App\ConceptoPagoResolucion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGratificacionEToConceptoPagoResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //limpiar tabla
        DB::table('concepto_pago_resoluciones')->truncate();
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/concepto_pago_resoluciones.json"));
        //Se llena el catalogo desde el arvhivo json concepto_pago_resoluciones.json
        foreach ($json->datos as $objeto){
            DB::table('concepto_pago_resoluciones')->insert([
                'nombre' => $objeto->nombre,
                'created_at' => date("Y-m-d H:d:s"),
                'updated_at' => date("Y-m-d H:d:s"),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('concepto_pago_resoluciones', function (Blueprint $table) {
            //
        });
    }
}
