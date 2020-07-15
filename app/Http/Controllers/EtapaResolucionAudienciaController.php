<?php

namespace App\Http\Controllers;

use App\EtapaResolucionAudiencia;
use Exception;
use Illuminate\Http\Request;

class EtapaResolucionAudienciaController extends Controller
{

    /**
     * Instancia del request
     * @var Request
     */
    protected $request;

    public function __construct(Request $request) {
        // $this->middleware("auth");
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
        try{
            $request->validate([
                'etapa_resolucion_id' => 'required',
                'audiencia_id' => 'required',
//                'evidencia' => 'required'
            ]);
            $etapaAudiencia = EtapaResolucionAudiencia::where('audiencia_id', $request->audiencia_id)->where('etapa_resolucion_id',$request->etapa_resolucion_id)->first();
            if($etapaAudiencia == null){
                $etapaAudiencia = EtapaResolucionAudiencia::create(
                [
                    "etapa_resolucion_id"=>$request->etapa_resolucion_id,
                    "audiencia_id"=>$request->audiencia_id,
                    "evidencia"=>isset($request->evidencia) ? $request->evidencia : true 
                ]);
            }else{
                $etapaAudiencia->update(["evidencia"=>$request->evidencia ]);
            }
            if ($this->request->wantsJson()) {
                return $this->sendResponse($etapaAudiencia, 'SUCCESS');
            }
        }catch(Exception $e){
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al guardar la etapa', 'Error');
            }
        }
        
            // return redirect('solicitudes')->with('success', 'Se ha creado la solicitud exitosamente');
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

    public function getEtapasAudiencia($id){
        $etapas = EtapaResolucionAudiencia::where('audiencia_id', $id)->orderBy('etapa_resolucion_id')->get();
        return $etapas;
    }
}
