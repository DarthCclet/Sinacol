<?php

namespace Tests\Feature;

use App\Audiencia;
use App\AudienciaParte;
use App\Compareciente;
use App\ConceptoPagoResolucion;
use App\Conciliador;
use App\ConciliadorAudiencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\ContadorController;
use App\Contacto;
use App\DatoLaboral;
use App\Domicilio;
use App\Expediente;
use App\Parte;
use App\Periodicidad;
use App\ResolucionParteConcepto;
use App\ResolucionPartes;
use App\Sala;
use App\SalaAudiencia;
use App\SalarioMinimo;
use App\Solicitud;
use App\VacacionesAnio;
use Carbon\Carbon;

class ConceptosResolucionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPropuestaCompletaConceptosConvenio()
    {
        $solicitud = Solicitud::first(); 
        if($solicitud->id != 0){
            $parteSolicitante = Parte::where('solicitud_id',$solicitud->id)->where('tipo_parte_id',1)->first();
            $datoLaboral = DatoLaboral::select('fecha_ingreso','fecha_salida','periodicidad_id','remuneracion')->where('parte_id',$parteSolicitante->id)->get();

            if(count($datoLaboral) >1){
                $datoLaboral =$datoLaboral->where('resolucion',true)->first();
            }else{
                $datoLaboral =$datoLaboral->where('resolucion',false)->first();
            }

            $diasPeriodicidad = Periodicidad::where('id', $datoLaboral->periodicidad_id)->first();
            $remuneracionDiaria = $datoLaboral->remuneracion / $diasPeriodicidad->dias;
                
            $anios_antiguedad = Carbon::parse($datoLaboral->fecha_ingreso)->floatDiffInYears($datoLaboral->fecha_salida);
            $propVacaciones = $anios_antiguedad - floor($anios_antiguedad);

            $salarios = SalarioMinimo::get('salario_minimo');
            $salarioMinimo = $salarios[0]->salario_minimo;
            $anioSalida = Carbon::parse($datoLaboral->fecha_salida)->startOfYear();
            $propAguinaldo = Carbon::parse($anioSalida)->floatDiffInYears($datoLaboral->fecha_salida);
            
            $vacacionesPorAnio = VacacionesAnio::all();
            $diasVacaciones = 0;
            foreach ($vacacionesPorAnio as $key => $vacaciones) {
                if($vacaciones->anios_laborados >= $anios_antiguedad ){
                    $diasVacaciones = $vacaciones->dias_vacaciones;
                    break;
                }
            }
            $pagoVacaciones = $propVacaciones * $diasVacaciones * $remuneracionDiaria;
            $salarioTopado = ($remuneracionDiaria > (2*$salarioMinimo) ? (2*$salarioMinimo) : $remuneracionDiaria);
            $prouestaCompleta = [];
            array_push($prouestaCompleta,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 5, "dias"=>90, "monto"=>round($remuneracionDiaria * 90,2))); //Indemnizacion constitucional = gratificacion A
            array_push($prouestaCompleta,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 4, "dias"=>15 * $propAguinaldo, "monto"=>round($remuneracionDiaria * 15 * $propAguinaldo,2))); //Aguinaldo = dias de aguinaldo
            array_push($prouestaCompleta,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 2, "dias"=>$propVacaciones * $diasVacaciones, "monto"=>round($pagoVacaciones,2))); //Vacaciones = dias vacaciones
            array_push($prouestaCompleta,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 3, "dias"=> $propVacaciones * $diasVacaciones * 0.25, "monto"=>round($pagoVacaciones * 0.25,2))); //Prima Vacacional
            array_push($prouestaCompleta,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 7, "dias"=>$anios_antiguedad *12, "monto"=>round($salarioTopado * $anios_antiguedad *12,2))); //Prima antiguedad = gratificacion C  
        }
        $this->assertInstanceOf('\App\Solicitud',$solicitud);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPropuesta50ConceptosConvenio()
    {
        $solicitud = Solicitud::first(); 
        if($solicitud->id != 0){
            $parteSolicitante = Parte::where('solicitud_id',$solicitud->id)->where('tipo_parte_id',1)->first();
            $datoLaboral = DatoLaboral::select('fecha_ingreso','fecha_salida','periodicidad_id','remuneracion')->where('parte_id',$parteSolicitante->id)->get();

            if(count($datoLaboral) >1){
                $datoLaboral =$datoLaboral->where('resolucion',true)->first();
            }else{
                $datoLaboral =$datoLaboral->where('resolucion',false)->first();
            }

            $diasPeriodicidad = Periodicidad::where('id', $datoLaboral->periodicidad_id)->first();
            $remuneracionDiaria = $datoLaboral->remuneracion / $diasPeriodicidad->dias;
                
            $anios_antiguedad = Carbon::parse($datoLaboral->fecha_ingreso)->floatDiffInYears($datoLaboral->fecha_salida);
            $propVacaciones = $anios_antiguedad - floor($anios_antiguedad);

            $salarios = SalarioMinimo::get('salario_minimo');
            $salarioMinimo = $salarios[0]->salario_minimo;
            $anioSalida = Carbon::parse($datoLaboral->fecha_salida)->startOfYear();
            $propAguinaldo = Carbon::parse($anioSalida)->floatDiffInYears($datoLaboral->fecha_salida);
            
            $vacacionesPorAnio = VacacionesAnio::all();
            $diasVacaciones = 0;
            foreach ($vacacionesPorAnio as $key => $vacaciones) {
                if($vacaciones->anios_laborados >= $anios_antiguedad ){
                    $diasVacaciones = $vacaciones->dias_vacaciones;
                    break;
                }
            }
            $pagoVacaciones = $propVacaciones * $diasVacaciones * $remuneracionDiaria;
            $salarioTopado = ($remuneracionDiaria > (2*$salarioMinimo) ? (2*$salarioMinimo) : $remuneracionDiaria);
            $prouestaAl50 = [];
            array_push($prouestaAl50,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 5, "dias"=>45, "monto"=>round($remuneracionDiaria * 45,2)));
            array_push($prouestaAl50,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 4, "dias"=>15 * $propAguinaldo, "monto"=>round($remuneracionDiaria * 15 * $propAguinaldo,2)));
            array_push($prouestaAl50,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 2, "dias"=>$propVacaciones * $diasVacaciones, "monto"=>round($pagoVacaciones,2)));
            array_push($prouestaAl50,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 3, "dias"=> $propVacaciones * $diasVacaciones * 0.25, "monto"=>round($pagoVacaciones * 0.25,2)));
            array_push($prouestaAl50,array("idSolicitante" => $parteSolicitante->id, "concepto_pago_resoluciones_id"=> 7, "dias"=>$anios_antiguedad *6, "monto"=>round($salarioTopado * $anios_antiguedad *6,2)));
        }
        $this->assertInstanceOf('\App\Solicitud',$solicitud);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConceptosConfigConvenio()
    {
        $solicitud = Solicitud::first(); 
        $parteSolicitante = Parte::where('solicitud_id',$solicitud->id)->where('tipo_parte_id',1)->first();
        if($solicitud->id != 0){
            $datoLaboral = DatoLaboral::select('fecha_ingreso','periodicidad_id','remuneracion')->where('parte_id',$parteSolicitante->id)->get();
            if(count($datoLaboral) >1){
                $datoLaboral =$datoLaboral->where('resolucion',true)->first();
            }else{
                $datoLaboral =$datoLaboral->where('resolucion',false)->first();
            }
            $diasPeriodicidad = Periodicidad::where('id', $datoLaboral->periodicidad_id)->first();
            $remuneracionDiaria = $datoLaboral->remuneracion / $diasPeriodicidad->dias;
            $conceptos = ConceptoPagoResolucion::all();
            foreach ($conceptos as $key => $concepto) {
                if($concepto->id==1 || $concepto->id==2 || $concepto->id==3  ){//Dias de sueldo, dias de vacaciones, prima vacacional
                    $numero_dias = random_int(1,500);
                    $monto = $numero_dias * $remuneracionDiaria;
                    $descConcepto = "";
                }elseif($concepto->id==4 ){//Aguinaldo
                    $numero_dias = random_int(1,500);
                    if($numero_dias <15){
                        $numero_dias = random_int(1,500);
                    }
                    $monto = $numero_dias * $remuneracionDiaria;
                }elseif($concepto->id==5 || $concepto->id==6){//Gratificación A,B
                    $numero_dias = random_int(1,500);
                    $monto = $numero_dias * $remuneracionDiaria;
                    $descConcepto = "";
                }elseif($concepto->id==7 ){ //Prima de antiguedad
                    $now = Carbon::now();
                    $anios_antiguedad = Carbon::parse($datoLaboral->fecha_ingreso)->floatDiffInYears($now);
                    // $antiguedad = round($antiguedad,2);
                    if($anios_antiguedad > 0){ //antiguedad 
                        $salario_minimo = SalarioMinimo::get('salario_minimo');
                        if($remuneracionDiaria < (2*$salario_minimo)){
                            $monto = $anios_antiguedad *12 * $remuneracionDiaria;
                        }else{
                            $monto = $anios_antiguedad *12 * $salario_minimo;
                        }
                    }
                }elseif($concepto->id==8){//Gratificación D
                    $monto = random_int(1,100000)/3;
                    $descConcepto = "";
                }
                $expediente = Expediente::where('solicitud_id', $solicitud->id)->first();
                $audiencia = Audiencia::where('expediente_id', $expediente->id)->first();
                
                $resolucion_partes = ResolucionPartes::where('audiencia_id', $audiencia->id);
                // $resolucion_parte_concepto=factory(ResolucionParteConcepto::class)->create([
                //     "resolucion_partes_id" => $resolucion_partes->id,
                //     "concepto_pago_resoluciones_id" => $concepto->id,
                //     "dias" => $numero_dias,
                //     "monto" => $monto,
                //     "otro" => $descConcepto,
                // ]);
            }
        }else{
            // se llama el factory de solicitud para crear un registro y probar su relacion
            $solicitud = factory(\App\Solicitud::class)->create();

            $solicitud->objeto_solicitudes()->sync([1]);
            // se crea parte solicitado
            $parteSolicitado = factory(Parte::class)->create(['solicitud_id'=>$solicitud->id]);
            $domicilioSolicitado = factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitado->id, 'domiciliable_type'=>'App\Parte']);
            $contactoSolicitado = factory(Contacto::class)->create(['contactable_id'=>$parteSolicitado->id, 'contactable_type'=>'App\Parte']);
            // se crea parte solicitado
            $parteSolicitante = factory(Parte::class)->create(['solicitud_id'=>$solicitud->id]);
            factory(Domicilio::class)->create(['domiciliable_id'=>$parteSolicitante->id, 'domiciliable_type'=>'App\Parte']);
            factory(DatoLaboral::class)->create(['parte_id'=>$parteSolicitante->id]);
            factory(Contacto::class)->create(['contactable_id'=>$parteSolicitante->id, 'contactable_type'=>'App\Parte']);
            
            $ContadorController = new ContadorController();
            $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
            $edo_folio = $solicitud->centro->abreviatura;
            $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
            $expediente = new Expediente();
            $expediente->folio = $folio;
            $expediente->anio = $folioC->anio;
            $expediente->consecutivo = $folioC->contador;
            $expediente->solicitud_id = $solicitud->id;
            $expediente->save();
            //Se captura Audiencia
            $sala = factory(Sala::class)->create();
            $audiencia = factory(Audiencia::class)->states('audienciaSimple')->create(['expediente_id'=>$expediente->id,'parte_responsable_id' => $parteSolicitante->id,'finalizada' =>true,'resolucion_id'=>1]);
            $compareciente_audiencia = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
            $compareciente_audiencia1 = factory(Compareciente::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
            $audiencia_parte_solicitante = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitante->id,]);
            $audiencia_parte_solicitado = factory(AudienciaParte::class)->create(['audiencia_id'=>$audiencia->id,'parte_id' => $parteSolicitado->id,]);
            $sala_audiencia = factory(SalaAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'sala_id' => $sala->id,]);
            $conciliador = factory(Conciliador::class)->create();
            $conciliador_audiencia = factory(ConciliadorAudiencia::class)->create(['audiencia_id'=>$audiencia->id,'conciliador_id' => $conciliador->id,]);
            $resolucion_partes=factory(ResolucionPartes::class)->create([
                "audiencia_id" => $audiencia->id,
                "parte_solicitante_id" => $parteSolicitante->id,
                "parte_solicitada_id" => $parteSolicitado->id
            ]);

            $datoLaboral = DatoLaboral::select('fecha_ingreso','periodicidad_id','remuneracion')->where('parte_id',$parteSolicitante->id)->get();
            if(count($datoLaboral) >1){
                $datoLaboral =$datoLaboral->where('resolucion',true)->first();
            }else{
                $datoLaboral =$datoLaboral->where('resolucion',false)->first();
            }
            $diasPeriodicidad = Periodicidad::where('id', $datoLaboral->periodicidad_id)->first();
            //  dd($diasPeriodicidad);
            $remuneracionDiaria = $datoLaboral->remuneracion / $diasPeriodicidad->dias;
            // dd($remuneracionDiaria);
            $conceptos = ConceptoPagoResolucion::all();
            foreach ($conceptos as $key => $concepto) {
                if($concepto->id==1 || $concepto->id==2 || $concepto->id==3  ){//Dias de sueldo, dias de vacaciones, prima vacacional
                    $numero_dias = random_int(1,500);
                    $monto = $numero_dias * $remuneracionDiaria;
                    $descConcepto = "";
                }elseif($concepto->id==4 ){//Aguinaldo
                    $numero_dias = random_int(1,500);
                    if($numero_dias <15){
                        $numero_dias = random_int(1,500);
                    }
                    $monto = $numero_dias * $remuneracionDiaria;
                }elseif($concepto->id==5 || $concepto->id==6){//Gratificación A,B

                }elseif($concepto->id==7 ){ //Prima de antiguedad
                    $now = Carbon::now();
                    $anios_antiguedad = Carbon::parse($datoLaboral->fecha_ingreso)->floatDiffInYears($now);
                    // $antiguedad = round($antiguedad,2);
                    if($anios_antiguedad > 0){ //antiguedad 
                        $salario_minimo = factory(SalarioMinimo::class)->create();
                        // $salario_minimo = SalarioMinimo::get('salario_minimo');
                        if($remuneracionDiaria <= (2*$salario_minimo)){
                            $monto = $anios_antiguedad *12 * $remuneracionDiaria;
                        }else{
                            $monto = $anios_antiguedad *12 * ($salario_minimo*2);
                        }
                    }
                }elseif($concepto->id==8){//Gratificación D
                    $monto = random_int(1,100000)/3;
                    $descConcepto = "";
                }
                $resolucion_parte_concepto=factory(ResolucionParteConcepto::class)->create([
                    "resolucion_partes_id" => $resolucion_partes->id,
                    "concepto_pago_resoluciones_id" => $concepto->id,
                    "dias" => $numero_dias,
                    "monto" => $monto,
                    "otro" => $descConcepto,
                ]);
            }
            $this->assertInstanceOf('\App\Solicitud',$solicitud);   
        }
    }
}
