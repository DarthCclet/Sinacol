<?php

use App\ResolucionParteConcepto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteDuplicatedToResolucionParteConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        unlink('/vagrant/deleted.txt');
        $deletedFile = fopen("/vagrant/deleted.txt", 'w');
        $arrayRPC = collect();
        $toDelete = collect();
        $resolucionesParteConceptos = ResolucionParteConcepto::all();
        foreach($resolucionesParteConceptos as $rpc){
            $rp = $rpc->ResolucionPartes;
            $arrayAux = collect(['concepto_pago_resoluciones_id'=>$rpc->concepto_pago_resoluciones_id,'dias'=>$rpc->dias,'audiencia_parte_id'=>$rpc->audiencia_parte_id]);
            $finded = $arrayRPC->search($arrayAux);
            if($finded){
                $toDelete->push($rpc->id);
            }else{
                $arrayRPC->push($arrayAux);
            }
        }
        fputs($deletedFile, json_encode($toDelete->toArray()));
        foreach($toDelete as $resolucionesParteConceptos){
            ResolucionParteConcepto::find($resolucionesParteConceptos)->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $deletes = file_get_contents('/vagrant/deleted.txt');
        $deletedArray = json_decode($deletes);
        foreach ($deletedArray as $key => $value) {
            $rpc = ResolucionParteConcepto::withTrashed()->find($value)->restore();
        }
    }
}
