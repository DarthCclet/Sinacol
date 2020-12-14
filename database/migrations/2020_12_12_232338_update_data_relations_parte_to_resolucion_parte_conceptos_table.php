<?php

use App\AudienciaParte;
use App\ResolucionParteConcepto;
use App\ResolucionPartes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataRelationsParteToResolucionParteConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $resolucionesParteConceptos = ResolucionParteConcepto::all();

        foreach($resolucionesParteConceptos as $rpc){
            $resolucionParte = ResolucionPartes::find($rpc->resolucion_partes_id);
            $audienciaParte = AudienciaParte::where('audiencia_id',$resolucionParte->audiencia_id)->where('parte_id',$resolucionParte->parte_solicitante_id)->first();
            if($audienciaParte){
                $rpc->audiencia_parte_id = $audienciaParte->id;
                $rpc->save();
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $resolucionesParteConceptos = ResolucionParteConcepto::all();
        foreach($resolucionesParteConceptos as $rpc){
            $rpc->audiencia_parte_id = null;
            $rpc->save();
        }
    }
}
