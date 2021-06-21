<?php

use App\Audiencia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CorrectAudienciaResolucionTerminacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $audiencias = Audiencia::where('tipo_terminacion_audiencia_id',5)->where('resolucion_id',null)->get();
        foreach ($audiencias as $audiencia) {
            $audiencia->update(['resolucion_id'=>2]);
        }
        $audiencias2 = Audiencia::where('tipo_terminacion_audiencia_id',null)->where('resolucion_id',null)->where('finalizada',true)->get();
        foreach ($audiencias2 as $audiencia) {
            $audiencia->update(['resolucion_id'=>2,'tipo_terminacion_audiencia_id'=>5]);
        }
        $audiencias3 = Audiencia::where('tipo_terminacion_audiencia_id',3)->get();
        foreach($audiencias3 as $audiencia3){
            if(!$audiencia3->EsUltima){
                $audiencia3->update(['resolucion_id'=>2]);
            }
        }
        $audiencias = Audiencia::with('expediente.solicitud')->where('tipo_terminacion_audiencia_id',null)->where('finalizada',true)->where('resolucion_id',3)->whereHas('expediente.solicitud',function($q){
            $q->where('estatus_solicitud_id',3)->where('incidencia',false);
            })->orderBy('id','asc')->get();
            foreach($audiencias as $aud){
              if(!$aud->EsUltima){
                $aud->update(['resolucion_id'=>2,'tipo_terminacion_audiencia_id'=>3]);
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
        //
    }
}
