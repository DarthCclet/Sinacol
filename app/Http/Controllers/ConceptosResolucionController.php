<?php

namespace App\Http\Controllers;

use App\DatoLaboral;
use App\Parte;
use App\Periodicidad;
use App\SalarioMinimo;
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
    public function getLaboralesConceptos(Request $request)//idSolicitante
    {
        // dd($request);
        $id = $this->request->get('solicitante_id');
        // $id = $this->request->get('id');
        // dd($id);
        $parteSolicitante = Parte::find($id);
        $datoLaboral = DatoLaboral::select('fecha_ingreso','periodicidad_id','remuneracion')->where('parte_id',$parteSolicitante->id)->get();
        // dd($datoLaboral);
        if(count($datoLaboral) >1){
            $datoLaboral =$datoLaboral->where('resolucion',true)->first();
        }else{
            $datoLaboral =$datoLaboral->where('resolucion',false)->first();
        }
        $diasPeriodicidad = Periodicidad::where('id', $datoLaboral->periodicidad_id)->first();
        $remuneracionDiaria = $datoLaboral->remuneracion / $diasPeriodicidad->dias;
        $now = Carbon::now();
        $anios_antiguedad = Carbon::parse($datoLaboral->fecha_ingreso)->floatDiffInYears($now);
        $salarios = SalarioMinimo::get('salario_minimo');
        // dd($salarios);
        $datosL = [];
        $datosL['remuneracionDiaria']= $remuneracionDiaria;
        $datosL['antiguedad']= $anios_antiguedad;
        $datosL['salarioMinimo']= $salarios[0]->salario_minimo;
        // dd($datosL);
        if ($this->request->wantsJson()) {
            return $this->sendResponse($datosL, 'SUCCESS');
        }

    }
}
