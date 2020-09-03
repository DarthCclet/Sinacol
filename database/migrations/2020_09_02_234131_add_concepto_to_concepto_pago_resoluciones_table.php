<?php

use App\ConceptoPagoResolucion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConceptoToConceptoPagoResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concepto_pago_resoluciones', function (Blueprint $table) {
        // Schema::table('tipo_documentos', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/concepto_pago_resoluciones.json"));
            //Se llena el catalogo desde el arvhivo json concepto_pago_resoluciones.json
            foreach ($json->datos as $objeto){
                $concepto = ConceptoPagoResolucion::find($objeto->id);
                $maxId = $objeto->id;
                if($concepto != null){
                    $concepto->update(['nombre'=>$objeto->nombre
                    ]);
                }else{
                    DB::table('concepto_pago_resoluciones')->insert(
                        [
                            'id' => $objeto->id,
                            'nombre' => $objeto->nombre
                        ]
                    );
                }
            }
            $tipo_documentos = ConceptoPagoResolucion::where('id','>',$maxId)->get();
            foreach ($tipo_documentos as $key => $objeto) {
                $objeto->delete();
            }
        });
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
