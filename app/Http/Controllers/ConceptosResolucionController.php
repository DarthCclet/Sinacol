<?php

namespace App\Http\Controllers;

use App\DatoLaboral;
use App\Ocupacion;
use App\Parte;
use App\Periodicidad;
use App\ResolucionParteConcepto;
use App\SalarioMinimo;
use App\VacacionesAnio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConceptosResolucionController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        // $this->docu = new ComunicacionCJF();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * Get datos para calcular conceptos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function guardarConceptosResolucion($conceptosResolucion = array())//idSolicitante
    {
        try {
            // if(isset($relacion["listaConceptosResolucion"])){
                // if(count($relacion["listaConceptosResolucion"]) > 0){
                    // foreach($conceptosResolucion as $concepto){
                    // // foreach($relacion["listaConceptosResolucion"] as $concepto){
                    //     ResolucionParteConcepto::create([
                    //         "resolucion_partes_id" => $resolucionParte->id,
                    //         "concepto_pago_resoluciones_id"=> $concepto["concepto_pago_resoluciones_id"],
                    //         "dias"=>$concepto["dias"],
                    //         "monto"=>$concepto["monto"],
                    //         "otro"=>$concepto["otro"]
                    //     ]);
                    // }
                // }
            // }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function getLaboralesConceptos(Request $request)//idSolicitante
    {
        try {
            $id = $this->request->get('solicitante_id');
            $parte = Parte::find($id);
            $datoLaboral = $parte->dato_laboral;
            $fechaSalida = "";
            if(count($datoLaboral) > 1){
                $datoLaborales =$datoLaboral->where('resolucion',true)->first();
            }else{
                $datoLaborales =$datoLaboral->where('resolucion',false)->first();
            }
            $diasPeriodicidad = Periodicidad::where('id', $datoLaborales->periodicidad_id)->first();
            $remuneracionDiaria = $datoLaborales->remuneracion / $diasPeriodicidad->dias;
            $labora_actualmente = $datoLaborales->labora_actualmente;
            if($labora_actualmente == false){
                $fechaSalida = Carbon::parse($datoLaborales->fecha_salida);
                $anios_antiguedad = Carbon::parse($datoLaborales->fecha_ingreso)->floatDiffInYears($datoLaborales->fecha_salida);
            }else{
                $fechaSalida = "Labora actualmente";
                $anios_antiguedad = Carbon::parse($datoLaborales->fecha_ingreso)->floatDiffInYears(Carbon::today());
            }
            $propVacaciones = $anios_antiguedad - floor($anios_antiguedad);
            $salarios = SalarioMinimo::get('salario_minimo');

            $tiempoVencido = Carbon::parse(Carbon::now())->diffInDays($datoLaborales->fecha_salida);
            $datosL = [];
            $salarioMinimo = $salarios[0]->salario_minimo;
            $datosL['idParte']= $id;
            $datosL['remuneracionDiaria']= $remuneracionDiaria;
            $datosL['antiguedad']= $anios_antiguedad;
            $datosL['salarioMinimo']= $salarioMinimo;
            $datosL['tiempoVencido']= $tiempoVencido;
            $datosL['salario']= $diasPeriodicidad->nombre .': $'.$datoLaborales->remuneracion ;
            $datosL['fechaIngreso']= Carbon::parse($datoLaborales->fecha_ingreso)->format('d/m/Y');
            $datosL['fechaSalida']= $fechaSalida;
            // $anioSalida = Carbon::parse($datoLaboral->fecha_salida);
            // $anioSalida = Carbon::createFromFormat('Y-m-d', $datoLaboral->fecha_salida)->year;
            $anioSalida = Carbon::parse($datoLaborales->fecha_salida)->startOfYear();
            $propAguinaldo = Carbon::parse($anioSalida)->floatDiffInYears($datoLaborales->fecha_salida);
            // dd($meses_vacaciones);
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
            
            //Propuesta de convenio al 100%
            $prouestaCompleta = [];
            array_push($prouestaCompleta,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 5, "dias"=>90, "monto"=>round($remuneracionDiaria * 90,2))); //Indemnizacion constitucional = gratificacion A
            array_push($prouestaCompleta,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 4, "dias"=>15 * $propAguinaldo, "monto"=>round($remuneracionDiaria * 15 * $propAguinaldo,2))); //Aguinaldo = dias de aguinaldo
            array_push($prouestaCompleta,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 2, "dias"=>$propVacaciones * $diasVacaciones, "monto"=>round($pagoVacaciones,2))); //Vacaciones = dias vacaciones
            array_push($prouestaCompleta,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 3, "dias"=> $propVacaciones * $diasVacaciones * 0.25, "monto"=>round($pagoVacaciones * 0.25,2))); //Prima Vacacional
            array_push($prouestaCompleta,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 7, "dias"=>$anios_antiguedad *12, "monto"=>round($salarioTopado * $anios_antiguedad *12,2))); //Prima antiguedad = gratificacion C
            
            $total = 0;
            $completa['indemnizacion']= round($remuneracionDiaria * 90,2);
            $total += $remuneracionDiaria * 90;
            $completa['aguinaldo']= round($remuneracionDiaria * 15 * $propAguinaldo,2);
            $total += $remuneracionDiaria * 15 * $propAguinaldo;
            $completa['vacaciones']= round($pagoVacaciones,2);
            $total += $pagoVacaciones;
            $completa['prima_vacacional']= round($pagoVacaciones * 0.25,2);
            $total += $pagoVacaciones * 0.25;
            $completa['prima_antiguedad']= round($salarioTopado * $anios_antiguedad *12,2);
            $total += $salarioTopado * $anios_antiguedad *12;
            $completa['total']= round($total,2);

            $datosL['completa']= $completa;
            
            //Propuesta de convenio al 50%
            $prouestaAl50 = [];
            array_push($prouestaAl50,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 5, "dias"=>45, "monto"=>round($remuneracionDiaria * 45,2)));
            array_push($prouestaAl50,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 4, "dias"=>15 * $propAguinaldo, "monto"=>round($remuneracionDiaria * 15 * $propAguinaldo,2)));
            array_push($prouestaAl50,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 2, "dias"=>$propVacaciones * $diasVacaciones, "monto"=>round($pagoVacaciones,2)));
            array_push($prouestaAl50,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 3, "dias"=> $propVacaciones * $diasVacaciones * 0.25, "monto"=>round($pagoVacaciones * 0.25,2)));
            array_push($prouestaAl50,array("idSolicitante" => $id, "concepto_pago_resoluciones_id"=> 7, "dias"=>$anios_antiguedad *6, "monto"=>round($salarioTopado * $anios_antiguedad *6,2)));

            $total = 0;
            $al50['indemnizacion']= round($remuneracionDiaria * 45,2);
            $total += $remuneracionDiaria * 45;
            $al50['aguinaldo']= round($remuneracionDiaria * 15 * $propAguinaldo,2);
            $total += $remuneracionDiaria * 15 * $propAguinaldo;
            $al50['vacaciones']= round($pagoVacaciones,2);
            $total += $pagoVacaciones;
            $al50['prima_vacacional']= round($pagoVacaciones * 0.25,2);
            $total += $pagoVacaciones * 0.25;
            $al50['prima_antiguedad']= round($salarioTopado * $anios_antiguedad * 6,2);
            $total += $salarioTopado * $anios_antiguedad * 6;
            $al50['total']= round($total,2);

            $datosL['al50']= $al50;
            $datosL['propuestaCompleta']= $prouestaCompleta;
            $datosL['propuestaAl50']= $prouestaAl50;

            // dd($datosL);
            if ($this->request->wantsJson()) {
                return $this->sendResponse($datosL, 'SUCCESS');
            }
        } catch (\Throwable $th) {
            $datosL = [];
            $datosL['error']= true;
            $datosL['mensaje']= "No se encontraron datos". $th;

            if ($this->request->wantsJson()) {
                return $this->sendResponse($datosL, 'ERROR');
            }
            // $datosL['antiguedad']= $anios_antiguedad;
            // $datosL['salarioMinimo']= $salarios[0]->salario_minimo;
            //throw $th;
        }
        
    }
    public function getLaboralesConceptosPre(Request $request)
    {
        try {
            $labora_actualmente = $request->labora_actualmente;
            $diasPeriodicidad = Periodicidad::where('id', $request->periodicidad_id)->first();
            $remuneracionDiaria = $request->remuneracion / $diasPeriodicidad->dias;
            if($labora_actualmente != ""){
                $anios_antiguedad = Carbon::parse($request->fecha_ingreso)->floatDiffInYears($request->fecha_salida);
            }else{
                $anios_antiguedad = Carbon::parse($request->fecha_ingreso)->floatDiffInYears(Carbon::now());
            }
            $anios_antiguedad_int = intval($anios_antiguedad);
            // dd($request->fecha_ingreso);
            $propVacaciones = $anios_antiguedad - floor($anios_antiguedad);
            if($request->ocupacion_id != "" && $request->ocupacion_id != null){
                $salarios = Ocupacion::find($request->ocupacion_id)->get('salario_resto_del_pais');
            }else{
                $salarios = SalarioMinimo::get('salario_minimo');
            }
            // dd($anios_antiguedad);
            $datosL = [];
            $salarioMinimo = $salarios[0]->salario_minimo;
            $datosL['remuneracionDiaria']= $remuneracionDiaria;
            $datosL['antiguedad']= $anios_antiguedad;
            $datosL['salarioMinimo']= $salarioMinimo;
            // $anioSalida = Carbon::parse($datoLaboral->fecha_salida);
            // $anioSalida = Carbon::createFromFormat('Y-m-d', $datoLaboral->fecha_salida)->year;
            $anioSalida = Carbon::parse($request->fecha_salida)->startOfYear();
            $propAguinaldo = Carbon::parse($anioSalida)->floatDiffInYears($request->fecha_salida);
            // dd($meses_vacaciones);
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
            
            //Propuesta de convenio al 100%
            $prouestaCompleta = [];
            array_push($prouestaCompleta,array( "concepto_pago_resoluciones_id"=> 5, "dias"=>90, "monto"=>round($remuneracionDiaria * 90,2))); //Indemnizacion constitucional = gratificacion A
            array_push($prouestaCompleta,array( "concepto_pago_resoluciones_id"=> 4, "dias"=>15 * $propAguinaldo, "monto"=>round($remuneracionDiaria * 15 * $propAguinaldo,2))); //Aguinaldo = dias de aguinaldo
            array_push($prouestaCompleta,array( "concepto_pago_resoluciones_id"=> 2, "dias"=>$propVacaciones * $diasVacaciones, "monto"=>round($pagoVacaciones,2))); //Vacaciones = dias vacaciones
            array_push($prouestaCompleta,array( "concepto_pago_resoluciones_id"=> 3, "dias"=> $propVacaciones * $diasVacaciones * 0.25, "monto"=>round($pagoVacaciones * 0.25,2))); //Prima Vacacional
            array_push($prouestaCompleta,array( "concepto_pago_resoluciones_id"=> 7, "dias"=>$anios_antiguedad *12, "monto"=>round($salarioTopado * $anios_antiguedad *12,2))); //Prima antiguedad = gratificacion C
            
            $total = 0;
            $completa['indemnizacion']= round($remuneracionDiaria * 90,2);
            $total += $remuneracionDiaria * 90;
            $completa['aguinaldo']= round($remuneracionDiaria * 15 * $propAguinaldo,2);
            $total += $remuneracionDiaria * 15 * $propAguinaldo;
            $completa['vacaciones']= round($pagoVacaciones,2);
            $total += $pagoVacaciones;
            $completa['prima_vacacional']= round($pagoVacaciones * 0.25,2);
            $total += $pagoVacaciones * 0.25;
            $completa['prima_antiguedad']= round($salarioTopado * $anios_antiguedad *12,2);
            $gratificacionB = ($anios_antiguedad_int * 20) * $remuneracionDiaria ;
            $completa['gratificacion_b'] = round($gratificacionB);
            $total += $salarioTopado * $anios_antiguedad *12;
            $completa['total']= round($total,2);
            $datosL['completa']= $completa;
            $datosL['anios_antiguedad']= $anios_antiguedad_int;
            //Propuesta de convenio al 50%
            $prouestaAl50 = [];
            array_push($prouestaAl50,array( "concepto_pago_resoluciones_id"=> 5, "dias"=>45, "monto"=>round($remuneracionDiaria * 45,2)));
            array_push($prouestaAl50,array( "concepto_pago_resoluciones_id"=> 4, "dias"=>15 * $propAguinaldo, "monto"=>round($remuneracionDiaria * 15 * $propAguinaldo,2)));
            array_push($prouestaAl50,array( "concepto_pago_resoluciones_id"=> 2, "dias"=>$propVacaciones * $diasVacaciones, "monto"=>round($pagoVacaciones,2)));
            array_push($prouestaAl50,array( "concepto_pago_resoluciones_id"=> 3, "dias"=> $propVacaciones * $diasVacaciones * 0.25, "monto"=>round($pagoVacaciones * 0.25,2)));
            array_push($prouestaAl50,array( "concepto_pago_resoluciones_id"=> 7, "dias"=>$anios_antiguedad *6, "monto"=>round($salarioTopado * $anios_antiguedad *6,2)));

            $total = 0;
            $al50['indemnizacion']= round($remuneracionDiaria * 45,2);
            $total += $remuneracionDiaria * 45;
            $al50['aguinaldo']= round($remuneracionDiaria * 15 * $propAguinaldo,2);
            $total += $remuneracionDiaria * 15 * $propAguinaldo;
            $al50['vacaciones']= round($pagoVacaciones,2);
            $total += $pagoVacaciones;
            $al50['prima_vacacional']= round($pagoVacaciones * 0.25,2);
            $total += $pagoVacaciones * 0.25;
            $al50['prima_antiguedad']= round($salarioTopado * $anios_antiguedad * 6,2);
            $total += $salarioTopado * $anios_antiguedad * 6;
            $al50['total']= round($total,2);

            $datosL['al50']= $al50;
            $datosL['propuestaCompleta']= $prouestaCompleta;
            $datosL['propuestaAl50']= $prouestaAl50;

            // dd($datosL);
            if ($this->request->wantsJson()) {
                return $this->sendResponse($datosL, 'SUCCESS');
            }
        } catch (\Throwable $th) {
            $datosL = [];
            $datosL['error']= true;
            $datosL['mensaje']= "No se encontraron datos". $th;

            if ($this->request->wantsJson()) {
                return $this->sendResponse($datosL, 'ERROR');
            }
            // $datosL['antiguedad']= $anios_antiguedad;
            // $datosL['salarioMinimo']= $salarios[0]->salario_minimo;
            //throw $th;
        }
        
    }
}
