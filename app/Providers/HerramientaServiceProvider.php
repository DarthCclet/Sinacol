<?php

namespace App\Providers;

use App\Audiencia;
use App\EtapaResolucionAudiencia;
use App\ResolucionParteConcepto;
use App\Solicitud;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class HerramientaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function rollback($solicitud_id,$audiencia_id,$tipoRollback){
        DB::beginTransaction();
        try{
            //tipoRollback nos indica que tipo de rollback se va a realizar, 1 antes de terminar audiencia, 2 antes de comparecencia, 3 antes de ratificación
            if($tipoRollback == 1){
                $solicitud = Solicitud::find($solicitud_id);
                $audiencia = Audiencia::find($audiencia_id);
                if($solicitud && $audiencia && $audiencia->finalizada){

                    $solicitud->estatus_solicitud_id = 2;
                    $solicitud->save();
                    $documentos = $audiencia->documentos()->whereIn('clasificacion_archivo_id',[15,16,17,18,41,43,52,54])->get();
                    $resolucionPartes =  $audiencia->resolucionPartes;
                    $audienciasPartes =  $audiencia->audienciaParte;
                    $pagosDiferidos =  $audiencia->pagosDiferidos;
                    foreach ($audienciasPartes as $key => $audienciaParte) {
                        $resolucionparteconcepto = ResolucionParteConcepto::where('audiencia_parte_id',$audienciaParte->id)->get();
                        foreach ($resolucionparteconcepto as $key => $parteConcepto) {
                            $parteConcepto->delete();
                        }
                    }
                    foreach ($resolucionPartes as $key => $resolucion) {
                        $resolucion->delete();
                    }
                    $etapaAudiencia = EtapaResolucionAudiencia::where('audiencia_id',$audiencia_id)->where('etapa_resolucion_id',6)->get();
                    foreach ($etapaAudiencia as $key => $etapa) {
                        $etapa->delete();
                    }
                    
                    foreach ($documentos as $key => $documento) {
                        $documento->delete();   
                    }
                    foreach ($pagosDiferidos as $key => $pagosDiferido) {
                        $pagosDiferido->delete();   
                    }
                    $audiencia->finalizada = false;
                    $audiencia->tipo_terminacion_audiencia_id = null;
                    $audiencia->resolucion_id = null;
                    $audiencia->reprogramada = false;
                    $audiencia->fecha_resolucion = null;
                    $audiencia->save();
                }else{
                    return ['success'=>false,'msj'=>' La audiencia no esta finalizada, no es posible hacer este proceso '];
                }
            }else if($tipoRollback == 2){
                $solicitud = Solicitud::find($solicitud_id);
                $audiencia = Audiencia::find($audiencia_id);
                if($solicitud && $audiencia && !$audiencia->finalizada){
                    $solicitud->estatus_solicitud_id = 2;
                    $solicitud->save();
                    $documentos = $audiencia->documentos()->whereIn('clasificacion_archivo_id',[15,16,17,18,41])->get();
                    $resolucionPartes =  $audiencia->resolucionPartes;
                    $audienciasPartes =  $audiencia->audienciaParte;
                    $pagosDiferidos =  $audiencia->pagosDiferidos;
                    $comparecientes =  $audiencia->comparecientes;
                    foreach ($audienciasPartes as $key => $audienciaParte) {
                        $resolucionparteconcepto = ResolucionParteConcepto::where('audiencia_parte_id',$audienciaParte->id)->get();
                        foreach ($resolucionparteconcepto as $key => $parteConcepto) {
                            $parteConcepto->delete();
                        }
                    }
                    foreach ($resolucionPartes as $key => $resolucion) {
                        $resolucion->delete();
                    }
                    $etapaAudiencia = EtapaResolucionAudiencia::where('audiencia_id',$audiencia_id)->get();
                    foreach ($etapaAudiencia as $key => $etapa) {
                        $etapa->delete();
                    }
                    
                    foreach ($documentos as $key => $documento) {
                        $documento->delete();   
                    }
                    foreach ($pagosDiferidos as $key => $pagosDiferido) {
                        $pagosDiferido->delete();   
                    }
                    foreach ($comparecientes as $key => $compareciente) {
                        $compareciente->delete();   
                    }
                    $audiencia->finalizada = false;
                    $audiencia->resolucion_id = null;
                    $audiencia->tipo_terminacion_audiencia_id = null;
                    $audiencia->reprogramada = false;
                    $audiencia->fecha_resolucion = null;
                    $audiencia->save();
                }else{
                    return ['success'=>false,'msj'=>' La audiencia esta finalizada, no es posible hacer este proceso '];
                }
                
            }else if($tipoRollback == 3){
                $solicitud = Solicitud::find($solicitud_id);
                if($solicitud != null && $solicitud->expediente){
                    $audiencias = $solicitud->expediente->audiencia;
                    $expediente = $solicitud->expediente;
                    if(count($audiencias) > 1){
                        return ['success'=>false,'msj'=>' No se puede realizar este proceso ya que esta solicitud tiene mas de una audiencia '];
                    }else{
                        $partes = $solicitud->partes;
                        $solicitud->estatus_solicitud_id = 1;
                        $solicitud->ratificada = false;
                        $solicitud->fecha_ratificacion = null;
                        $solicitud->save();
                        $audiencia = Audiencia::find($audiencia_id);
                        if($audiencia != null){
                            $documentos = $audiencia->documentos;
                            $resolucionPartes =  $audiencia->resolucionPartes;
                            $audienciasPartes =  $audiencia->audienciaParte;
                            $pagosDiferidos =  $audiencia->pagosDiferidos;
                            $comparecientes =  $audiencia->comparecientes;
                            $salasAudiencias =  $audiencia->salasAudiencias;
                            $conciliadoresAudiencias =  $audiencia->conciliadoresAudiencias;
                            foreach ($audienciasPartes as $key => $audienciaParte) {
                                $resolucionparteconcepto = ResolucionParteConcepto::where('audiencia_parte_id',$audienciaParte->id)->get();
                                foreach ($resolucionparteconcepto as $key => $parteConcepto) {
                                    $parteConcepto->delete();
                                }
                            }
                            foreach ($resolucionPartes as $key => $resolucion) {
                                $resolucion->delete();
                            }
                            $etapaAudiencia = EtapaResolucionAudiencia::where('audiencia_id',$audiencia_id)->get();
                            foreach ($etapaAudiencia as $key => $etapa) {
                                $etapa->delete();
                            }
                            
                            foreach ($documentos as $key => $documento) {
                                $documento->delete();   
                            }
                            foreach ($pagosDiferidos as $key => $pagosDiferido) {
                                $pagosDiferido->delete();   
                            }
                            foreach ($comparecientes as $key => $compareciente) {
                                $compareciente->delete();   
                            }
                            foreach ($salasAudiencias as $key => $salasAudiencia) {
                                $salasAudiencia->delete();   
                            }
                            foreach ($conciliadoresAudiencias as $key => $conciliadoresAudiencia) {
                                $conciliadoresAudiencia->delete();   
                            }
                            $audiencia->delete();
                        }
                        foreach ($partes as $key => $parte) {
                            $documentosParte = $parte->documentos;
                            foreach ($documentosParte as $key => $document) {
                                $document->delete();
                            }
                            if($parte->tipo_parte_id == 3){
                                $parte->delete();   
                            }
                        }
                        $expediente->delete();
                    }
                }else{
                    return ['success'=>false,'msj'=>' Esta solicitud no esta confirmada, no es posible realizar el proceso '];
                }

            }else if($tipoRollback == 4){
                $solicitud = Solicitud::find($solicitud_id);
                if($solicitud != null && $solicitud->expediente){
                    $expediente = $solicitud->expediente;
                    $expediente->delete();
                    $solicitud->estatus_solicitud_id = 1;
                    $solicitud->ratificada = false;
                    $solicitud->fecha_ratificacion = null;
                    $solicitud->justificacion_incidencia = "";
                    $solicitud->incidencia = false;
                    $solicitud->save();
                    $documentos = $solicitud->documentos->where('clasificacion_archivo_id',13);
                    foreach ($documentos as $key => $documento) {
                        $documento->delete();   
                    }
                }else{
                    return ['success'=>false,'msj'=>' Esta solicitud no esta confirmada, no es posible realizar el proceso '];
                }

            }else{
                return ['success'=>false,'msj'=>' No se selecciono proceso a ejecutar'];
            }
            DB::commit();
            return ['success'=>true,'msj'=>' Proceso realizado correctamente '];

        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                " Se emitió el siguiente mensaje: ". $e->getMessage().
                " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return ['success'=>false,'msj'=>'Error al realizar el proceso '];
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
