<?php

use App\ConceptoPagoResolucion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDataToConceptoPagoResolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concepto_pago_resoluciones', function (Blueprint $table) {
            $path = base_path('database/datafiles');
            $json = json_decode(file_get_contents($path . "/concepto_pago_resoluciones.json"));
            //Se llena el catalogo desde el arvhivo json objeto_solicitudes.json
            foreach ($json->datos as $concepto_pago_resoluciones){
                $maxId = $concepto_pago_resoluciones->id;
                $concepto_pago = ConceptoPagoResolucion::find($concepto_pago_resoluciones->id);
                if($concepto_pago != null){
                    $concepto_pago->nombre = $concepto_pago_resoluciones->nombre;
                    $concepto_pago->save();
                }else{
                    DB::table('concepto_pago_resoluciones')->insert(
                        [
                            'id' => $concepto_pago_resoluciones->id,
                            'nombre' => $concepto_pago_resoluciones->nombre,
                            'created_at' => date("Y-m-d H:d:s"),
                            'updated_at' => date("Y-m-d H:d:s")
                        ]
                    );
                }
            }
            $concepto_pago_resolucionesExtra = ConceptoPagoResolucion::where('id','>',$maxId)->get();
            foreach ($concepto_pago_resolucionesExtra as $key => $value) {
                $value->delete();
            }
        });
        Artisan::call('cache:clear');
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
