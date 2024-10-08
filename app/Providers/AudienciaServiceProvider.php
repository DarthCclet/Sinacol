<?php

namespace App\Providers;

use App\Audiencia;
use App\AudienciaParte;
use App\Compareciente;
use App\Events\GenerateDocumentResolution;
use App\Events\RatificacionRealizada;
use App\Parte;
use App\ResolucionPagoDiferido;
use App\ResolucionParteConcepto;
use App\ResolucionPartes;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AudienciaServiceProvider extends ServiceProvider
{

    /**
     * Funcion para guardar las resoluciones individuales de las audiencias
     * @param Audiencia $audiencia
     * @param type $arrayRelaciones
     */
    public static function guardarRelaciones(Audiencia $audiencia, $arrayRelaciones = array(), $listaConceptos = array(), $listaFechasPago = array(), $listaTipoPropuestas = array()) {
        $partes = $audiencia->audienciaParte;
        $solicitantes = self::getSolicitantes($audiencia);
        $solicitados = self::getSolicitados($audiencia);
        $huboConvenio = false;
        $totalPagoC = [];
        $totalDeduccionC = [];
        foreach ($solicitados as $solicitado) {
            foreach ($solicitantes as $solicitante) {
                $bandera = true;
                if ($arrayRelaciones != null) {
                    foreach ($arrayRelaciones as $relacion) {
                        //
                        $parte_solicitante = Parte::find($relacion["parte_solicitante_id"]);
                        if ($parte_solicitante->tipo_parte_id == 3) {
                            $parte_solicitante = Parte::find($parte_solicitante->parte_representada_id);
                        }
                        //
                        $parte_solicitado = Parte::find($relacion["parte_solicitado_id"]);
                        if ($parte_solicitado->tipo_parte_id == 3) {
                            $parte_solicitado = Parte::find($parte_solicitado->parte_representada_id);
                        }

                        if ($solicitante->parte_id == $parte_solicitante->id && $solicitado->parte_id == $parte_solicitado->id) {
                            $terminacion = 3;
                            $huboConvenio = true;
                        } else {
                            $terminacion = 5;
                        }
                        $bandera = false;
                        $resolucionParte = ResolucionPartes::create([
                            "audiencia_id" => $audiencia->id,
                            "parte_solicitante_id" => $solicitante->parte_id,
                            "parte_solicitada_id" => $solicitado->parte_id,
                            "terminacion_bilateral_id" => $terminacion
                        ]);
                    }
                }
                
                if ($bandera) {
                    //Se consulta comparecencia de solicitante
                    $parteS = $solicitante->parte;
                    $comparecienteSol = null;
                    if ($parteS->tipo_persona_id == 2) {//solicitante moral
                        $compareciente_partes = Parte::where("parte_representada_id", $parteS->id)->get();
                        foreach ($compareciente_partes as $key => $compareciente_parte) {
                            $comparecienteSolicitud = Compareciente::where('parte_id', $compareciente_parte->id)->where('audiencia_id',$audiencia->id)->first();
                            if($comparecienteSolicitud != null){
                                $comparecienteSol = $comparecienteSolicitud;
                            }
                        }
                        
                    } else {//solicitante fisica
                        $comparecienteSol = Compareciente::where('parte_id', $solicitante->parte_id)->where('audiencia_id',$audiencia->id)->first();
                        if($comparecienteSol == null) {
                            $compareciente_partes = Parte::where("parte_representada_id", $parteS->id)->get();
                            if (count($compareciente_partes) > 0) {
                                foreach ($compareciente_partes as $key => $compareciente_parte) {
                                    $comparecienteSol = Compareciente::where('parte_id', $compareciente_parte->id)->where('audiencia_id',$audiencia->id)->first();
                                }
                            } 
                        }
                    }
                    //Se consulta comparecencia de citado
                    $comparecienteCit = null;
                    $comparecienteCit = Compareciente::where('parte_id', $solicitado->parte_id)->where('audiencia_id',$audiencia->id)->first();
                    if ($comparecienteCit == null) {
                        $compareciente_partes = Parte::where("parte_representada_id", $solicitado->parte_id)->get();
                        foreach ($compareciente_partes as $key => $compareciente_parte) {
                            $comparecienteCitado = Compareciente::where('parte_id', $compareciente_parte->id)->where('audiencia_id',$audiencia->id)->first();
                            if($comparecienteCitado != null){
                                $comparecienteCit = $comparecienteCitado;
                            }
                        } 
                    }

                    $terminacion = 1;
                    if ($audiencia->resolucion_id == 3) {//no hubo convenio, guarda resolucion para todas las partes
                        $terminacion = 5;
                        //se genera el acta de no conciliacion para todos los casos
                        event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 17, 1, $solicitante->parte_id, $solicitado->parte_id));

                        $parte = $solicitado->parte;
                        if ($parte->tipo_persona_id == 2) {
                            $compareciente_parte = Parte::where("parte_representada_id", $parte->id)->first();
                            if ($compareciente_parte != null) {
                                $compareciente = Compareciente::where('parte_id', $compareciente_parte->id)->where('audiencia_id',$audiencia->id)->first();
                            } else {
                                $compareciente = null;
                            }
                        } else {
                            $compareciente_parte = Parte::where("parte_representada_id", $parte->id)->first();
                            if ($compareciente_parte != null) {
                                $compareciente = Compareciente::where('parte_id', $compareciente_parte->id)->where('audiencia_id',$audiencia->id)->first();
                            } else {
                                // $compareciente = null;
                                $compareciente = Compareciente::where('parte_id', $solicitado->parte_id)->where('audiencia_id',$audiencia->id)->first();
                            }
                        }
                    } else if ($audiencia->resolucion_id == 1) {// Hubo convenio
                        if ($comparecienteSol != null && $comparecienteCit != null) {
                            $terminacion = 3;
                            $huboConvenio = true;
                        } else if ($comparecienteSol != null) {
                            $terminacion = 5;
                        } else {
                            $terminacion = 1;
                        }
                    } else if ($audiencia->resolucion_id == 2) {
                        //no hubo convenio pero se agenda nueva audiencia, guarda para todos las partes
                        $terminacion = 2;
                        // event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,52,2,$solicitante->parte_id,$solicitado->parte_id));
                    }
                    $resolucionParte = ResolucionPartes::create([
                                "audiencia_id" => $audiencia->id,
                                "parte_solicitante_id" => $solicitante->parte_id,
                                "parte_solicitada_id" => $solicitado->parte_id,
                                "terminacion_bilateral_id" => $terminacion
                    ]);
                }
                //guardar conceptos de pago para Convenio
                if (isset($resolucionParte)) { //Hubo conciliacion
                    if ($terminacion == 3) {
                        $huboConvenio = true;
                        //Se consulta comparecencia de citado
                        $parte = $solicitado->parte;
                        if ($parte->tipo_persona_id == 2) {
                            $compareciente_parte = Parte::where("parte_representada_id", $parte->id)->first();
                            if ($compareciente_parte != null) {
                                $comparecienteCit = Compareciente::where('parte_id', $compareciente_parte->id)->first();
                            } else {
                                $comparecienteCit = null;
                            }
                        } else {
                            $comparecienteCit = Compareciente::where('parte_id', $solicitado->parte_id)->first();
                        }
                        // Termina consulta de comparecencia de citado
                        //Se consulta comparecencia de solicitante
                        $parteS = $solicitante->parte;
                        if ($parteS->tipo_persona_id == 2) {
                            $compareciente_parte = Parte::where("parte_representada_id", $parteS->id)->first();
                            if ($compareciente_parte != null) {
                                $comparcomparecienteSoleciente = Compareciente::where('parte_id', $compareciente_parte->id)->first();
                            } else {
                                $comparecienteSol = null;
                            }
                        } else {
                            $comparecienteSol = Compareciente::where('parte_id', $solicitante->parte_id)->first();
                        }
                    }
                }
            }
            $solicitanteComparecio = $solicitado->parte->compareciente->where('audiencia_id', $audiencia->id)->first();
            if ($solicitanteComparecio != null) {
                if (isset($listaConceptos)) {
                    if (count($listaConceptos) > 0) {
                        $totalPagoC[$solicitado->parte_id] = 0;
                        $totalDeduccionC[$solicitado->parte_id] = 0.0;
                        foreach ($listaConceptos as $key => $conceptosSolicitante) {//solicitantes
                            if ($key == $solicitado->parte_id) {
                                foreach ($conceptosSolicitante as $k => $concepto) {
                                    ResolucionParteConcepto::create([
                                        "resolucion_partes_id" => null, //$resolucionParte->id,
                                        "audiencia_parte_id" => $solicitado->id,
                                        "concepto_pago_resoluciones_id" => $concepto["concepto_pago_resoluciones_id"],
                                        "dias" => intval($concepto["dias"]),
                                        "monto" => $concepto["monto"],
                                        "otro" => $concepto["otro"]
                                    ]);
                                    //$totalPagoC[$solicitado->parte_id] =  floatval($totalPagoC[$solicitado->parte_id])  + floatval($concepto["monto"]);
                                    if($concepto["concepto_pago_resoluciones_id"] == 13){
                                        $totalDeduccionC[$solicitado->parte_id] = floatval($totalDeduccionC[$solicitado->parte_id])  + floatval($concepto["monto"]);
                                    }else{
                                        $totalPagoC[$solicitado->parte_id] =  floatval($totalPagoC[$solicitado->parte_id])  + floatval($concepto["monto"]);
                                    }
                                }
                            }
                        }
                        foreach ($listaTipoPropuestas as $key => $listaPropuesta) {//solicitantes
                            if ($key == $solicitado->parte_id) {
                                $resolucionParte = ResolucionPartes::where("audiencia_id",$audiencia->id)->where("parte_solicitada_id",$solicitado->parte_id)->where("parte_solicitante_id",$solicitante->parte_id)->get();
                                foreach ($resolucionParte as $resolucion) {//solicitantes
                                    $resolucion->update([
                                        "tipo_propuesta_pago_id" => $listaPropuesta
                                    ]);
                                }
                            }
                        }

                    }
                }
            }
        }
        // Termina consulta de comparecencia de solicitante
        if ($huboConvenio) {
            if (isset($listaFechasPago)) { //se registran pagos diferidos
                if (count($listaFechasPago) > 0) {
                    //$totalPagoC[$solicitado->parte_id] = $totalPagoC[$solicitado->parte_id] - $totalDeduccionC[$solicitado->parte_id];
                    foreach ($listaFechasPago as $key => $fechaPago) {
                        ResolucionPagoDiferido::create([
                            "audiencia_id" => $audiencia->id,
                            "solicitante_id" => $fechaPago["idCitado"],
                            "monto" => $fechaPago["monto_pago"],
                            "descripcion_pago" => $fechaPago["descripcion_pago"],
                            "fecha_pago" => Carbon::createFromFormat('d/m/Y h:i', $fechaPago["fecha_pago"])->format('Y-m-d h:i'),
                            "diferido"=>true
                        ]);
                    }
                }
            }
            foreach ($solicitados as $solicitado) {
                foreach ($solicitantes as $solicitante) {
                    $part = Parte::find($solicitado->parte_id);
                    $datoLaboral_citado = $part->dato_laboral()->orderBy('id', 'desc')->first();
                    if ($datoLaboral_citado->labora_actualmente) {
                        $date = Carbon::now();
                        $datoLaboral_citado->fecha_salida = $date;
                        $datoLaboral_citado->save();
                    }
                    $convenio = ResolucionPartes::where('parte_solicitante_id', $solicitante->parte_id)->where('parte_solicitada_id', $solicitado->parte_id)->where('terminacion_bilateral_id', 3)->first();
                    if ($convenio != null) {
                        if (!isset($listaFechasPago)) {//si no se registraron pagos diferidos crear pago NO diferido
                            $totalPagoC[$solicitado->parte_id] = $totalPagoC[$solicitado->parte_id] - $totalDeduccionC[$solicitado->parte_id];
                            ResolucionPagoDiferido::create([
                                "audiencia_id" => $audiencia->id,
                                "solicitante_id" => $solicitado->parte_id,
                                "monto" => $totalPagoC[$solicitado->parte_id],
                                "descripcion_pago" => "Convenio",
                                "fecha_pago" => $audiencia->fecha_audiencia ." ".$audiencia->hora_fin,
                                "diferido"=>false
                            ]);
                        }

                        if($convenio->tipo_propuesta_pago_id == 4){
                            //generar convenio reinstalacion
                            event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 43, 15, $solicitante->parte_id, $solicitado->parte_id));
                        }elseif($convenio->tipo_propuesta_pago_id == 5){
                            //generar convenio de prestaciones
                            event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 16, 17, $solicitante->parte_id, $solicitado->parte_id));
                        }else{
                            //generar convenio
                            event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 52, 14, $solicitante->parte_id, $solicitado->parte_id));//15
                        }

                        
                    } else {
                        // $noConciliacion = ResolucionPartes::where('parte_solicitante_id', $solicitante->parte_id)->where('terminacion_bilateral_id', 5)->first();
                        // if ($noConciliacion != null) {
                        event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 17, 1, $solicitante->parte_id, $solicitado->parte_id));
                        // }
                    }
                }
            }
        }
        $solicitud = $audiencia->expediente->solicitud();
        $solicitud->update(['url_virtual' => null]);
        //generar acta de audiencia
        event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 15, 3));
    }

    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitante
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitante
     */
    public static function getSolicitantes(Audiencia $audiencia) {
        $solicitantes = [];
        foreach ($audiencia->audienciaParte as $parte) {
            if ($parte->parte->tipo_parte_id == 1) {
                $solicitantes[] = $parte;
            }
        }
        return $solicitantes;
    }

    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitado
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitado
     */
    public static function getSolicitados(Audiencia $audiencia) {
        $solicitados = [];
        foreach ($audiencia->audienciaParte as $parte) {
            if ($parte->parte->tipo_parte_id == 2) {
                $solicitados[] = $parte;
            }
        }
        return $solicitados;
    }
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
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
