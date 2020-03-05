<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Audiencia;
use App\Conciliador;
use App\Sala;
use App\AgendaAudiencia;
use App\ConciliadorAudiencia;
use App\SalaAudiencia;
use App\Centro;
use Validator;
use App\Filters\CatalogoFilter;
use Illuminate\Support\Facades\DB;

class AudienciaController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        return Audiencia::all();
        Audiencia::with('conciliador')->get();
        // $solicitud = Solicitud::all();


        // Filtramos los usuarios con los parametros que vengan en el request
        $audiencias = (new CatalogoFilter(Audiencia::query(), $this->request))
            ->searchWith(Audiencia::class)
            ->filter();

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $audiencias = $audiencias->get();
        } else {
            $audiencias = $audiencias->paginate($this->request->get('per_page', 10));
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $audiencias = tap($audiencias)->each(function ($audiencia) {
            $audiencia->loadDataFromRequest();
        });

        // return $this->sendResponse($solicitud, 'SUCCESS');

        if ($this->request->wantsJson()) {
            return $this->sendResponse($audiencias, 'SUCCESS');
        }
        return view('expediente.audiencias.index', compact('audiencias'));
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
        $validator = Validator::make($request->all(), [
            'expediente_id' => 'required|Integer',
            'conciliador_id' => 'required|Integer',
            'sala_id' => 'required|Integer',
            'resolucion_id' => 'required|Integer',
            'parte_responsable_id' => 'required|Integer',
            'fecha_audiencia' => 'required|Date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_audiencia' => 'required|Integer',
            'reprogramada' => 'required|Boolean',
            'desahogo' => 'required|max:3000',
            'convenio' => 'required|max:3000'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Audiencia::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Audiencia::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Audiencia $audiencia)
    {
        // obtenemos los conciliadores
        $partes = DB::table('partes')
                ->join('tipo_partes', 'partes.tipo_parte_id', '=', 'tipo_partes.id')
                ->join('expedientes', 'partes.solicitud_id', '=', 'expedientes.solicitud_id')
                ->where("expedientes.id","=",$audiencia->expediente_id)
                ->select("partes.*","tipo_partes.nombre as nombreParte")
                ->get();
        $agenda = DB::table('agenda_audiencias')
                ->join('conciliadores', 'agenda_audiencias.conciliador_id', '=', 'conciliadores.id')
                ->join('personas', 'conciliadores.persona_id', '=', 'personas.id')
                ->join('salas', 'agenda_audiencias.sala_id', '=', 'salas.id')
                ->where("agenda_audiencias.audiencia_id","=",$audiencia->id)
                ->where("agenda_audiencias.deleted_at","=",null)
                ->select("personas.*","conciliadores.*","salas.*","agenda_audiencias.solicitante")
                ->get();
        $audiencia->partes = $partes;
        $audiencia->agenda = $agenda;
//        dd($audiencia);
        return view('expediente.audiencias.edit', compact('audiencia'));
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
        $validator = Validator::make($request->all(), [
            'expediente_id' => 'required|Integer',
            'conciliador_id' => 'required|Integer',
            'sala_id' => 'required|Integer',
            'resolucion_id' => 'required|Integer',
            'parte_responsable_id' => 'required|Integer',
            'fecha_audiencia' => 'required|Date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_audiencia' => 'required|Integer',
            'reprogramada' => 'required|Boolean',
            'desahogo' => 'required|max:3000',
            'convenio' => 'required|max:3000'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $res = Audiencia::find($id);
        $res->update($request->all());
        return $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $audiencia = Audiencia::findOrFail($id)->delete();
        return 204;
    }
    
    /**
     * Muestra el calendario de las audiencias a celebrar
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function calendario()
    {
        return view('expediente.audiencias.calendario');
    }
    /**
     * Funcion para obtener los conciliadores disponibles
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ConciliadoresDisponibles(Request $request)
    {
        $fechaInicio = $request->fechaInicio;
        $diaSemana = date('N',strtotime($request->fechaInicio));
        $fechaFin = $request->hora_fin;
        $conciliadores = Conciliador::all();
        $conciliadoresResponse=[];
        foreach($conciliadores as $conciliador){
            $pasa=false;
            if(count($conciliador->disponibilidades) > 0){
                foreach($conciliador->disponibilidades as $disp){
                    if($disp["dia"] == $diaSemana){
                        $pasa = true;
                    }
                }
            }else{$pasa=false;}
            if($pasa){
                foreach($conciliador->incidencias as $inci){
                    if($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]){
                        $pasa=false;
                    }
                }
            }
            if($pasa){
                $conciliador->persona = $conciliador->persona;
                $conciliadoresResponse[]=$conciliador;
            }
        }
        return $conciliadoresResponse;
    }
    public function SalasDisponibles(Request $request)
    {
        $fechaInicio = $request->fechaInicio;
        $diaSemana = date('N',strtotime($request->fechaInicio));
        $fechaFin = $request->hora_fin;
        $salas = Sala::all();
        $salasResponse=[];
        foreach($salas as $sala){
            $pasa=false;
            if(count($sala->disponibilidades) > 0){
                foreach($sala->disponibilidades as $disp){
                    if($disp["dia"] == $diaSemana){
                        $pasa = true;
                    }
                }
            }else{$pasa=false;}
            if($pasa){
                foreach($sala->incidencias as $inci){
                    if($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]){
                        $pasa=false;
                    }
                }
            }
//            dd($pasa);
            if($pasa){
                $salasResponse[]=$sala;
            }
        }
        return $salasResponse;
    }
    /**
     * Funcion para obtener la vista del calendario
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function calendarizar(Request $request)
    {
        $audiencia = Audiencia::find($request->audiencia_id);
        $id_conciliador = null;
        AgendaAudiencia::where("audiencia_id","=",$request->audiencia_id)->delete();
        foreach ($request->asignacion as $value) {
            if($value["resolucion"]){
                $id_conciliador = $value["conciliador"];
            }
            AgendaAudiencia::create(["audiencia_id" => $request->audiencia_id, "conciliador_id" => $value["conciliador"],"sala_id" => $value["sala"],"solicitante" => $value["resolucion"]]);
        }
        if($request->tipoAsignacion == 1){
            $multiple = false;
        }else{
            $multiple = true;
        }
        $audiencia->update(["fecha_audiencia" => $request->fecha_audiencia,"hora_inicio" => $request->hora_inicio, "hora_fin" => $request->hora_fin,"conciliador_id" => $id_conciliador,"multiple"=>$multiple]);
        return $audiencia;
    }
    /**
     * Funcion para obtener los momentos ocupados
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCalendario(Request $request)
    {
        // inicio obtenemos los datos del centro donde no trabajará
        $centro = Centro::find($request->centro_id);
        $centroDisponibilidad = $centro->disponibilidades;
        $laboresCentro=array();
        foreach($centroDisponibilidad as $key => $value){
            array_push($laboresCentro,array("dow" => array($value["dia"]),"startTime" => $value["hora_inicio"], "endTime" => $value["hora_fin"]));
        }
        //fin obtenemos disponibilidad
        //inicio obtenemos incidencias del centro
        $incidenciasCentro=array();
        $centroIncidencias = $centro->incidencias;
        $arrayFechas=[];
        foreach($centroIncidencias as $key => $value){
            $arrayFechas = $this->validarIncidenciasCentro($value,$arrayFechas);
        }
        $arrayAudiencias=$this->getTodasAudiencias($request->centro_id);
        $ev=array_merge($arrayFechas,$arrayAudiencias);
        //construimos el arreglo general
        $arregloGeneral = array();
        $arregloGeneral["laboresCentro"] = $laboresCentro;
        $arregloGeneral["incidenciasCentro"] = $ev;
        $arregloGeneral["duracionPromedio"] = $centro->duracionAudiencia;
        return $arregloGeneral;
    }
    /**
     * Funcion para desglosar las fechas de las incidencias
     * @param type $incidencia
     * @param type $arrayFechas
     * @return string
     */
    public function validarIncidenciasCentro($incidencia,$arrayFechas){
        $dates = array();
        $current = strtotime($incidencia["fecha_inicio"]);
        $last = strtotime($incidencia["fecha_fin"]);
        $step = '+1 day';
        $output_format = 'Y-m-d';
        while($current <= $last) {
            $arrayFechas[] = array("start" => date($output_format, $current) ,"end" => date($output_format, $current), "rendering" => 'background',"backgroundColor" => 'red',"allDay" => true);
            $arrayFechas[] = array("start" => date($output_format, $current)." 00:00:00" ,"end" => date($output_format, $current)." 23:59:00", "rendering" => 'background',"backgroundColor" => 'red',"allDay" => false);
            $current = strtotime($step, $current);
        }
        return $arrayFechas;
    }
    /**
     * Funcion para obtener las audiencias de el centro
     * @param id $centro_id
     * @return array
     */
    public function getTodasAudiencias($centro_id){
        $Audiencias = DB::table('audiencias')
                ->join('expedientes', 'audiencias.expediente_id', '=', 'expedientes.id')
                ->join('solicitudes', 'expedientes.solicitud_id', '=', 'solicitudes.id')
                ->where("solicitudes.centro_id","=",$centro_id)
                ->where("audiencias.fecha_audiencia",">",date("Y-m-d"))
                ->select("audiencias.*","expedientes.folio","expedientes.anio")
                ->get();
        $arrayEventos=[];
        foreach($Audiencias as $audiencia){
            $start = $audiencia->fecha_audiencia ." ". $audiencia->hora_inicio;
            $end = $audiencia->fecha_audiencia ." ". $audiencia->hora_fin;
            array_push($arrayEventos,array("start" => $start ,"end" => $end,"title" => $audiencia->folio."/".$audiencia->anio,"color" => "#00ACAC"));
        }
        return $arrayEventos;
    }
    /**
     * Funcion para guardar la resolución de la audiencia
     * @param id $centro_id
     * @return array
     */
    function Resolucion(Request $request){
        $audiencia = Audiencia::find($request->audiencia_id);
        $audiencia->update(array("convenio" => $request->convenio,"desahogo" => $request->desahogo,"resolucion_id"=>$request->resolucion_id));
        return $audiencia;
    }
    /**
     * 
     * @param Conciliador $conciliador
     * @return Audiencias
     */
    public function getAgenda(Request $request){
        $conciliador = Conciliador::find($request->id);
        $audiencias = DB::table('agenda_audiencias')
                ->join('salas', 'agenda_audiencias.sala_id', '=', 'salas.id')
                ->join('audiencias', 'agenda_audiencias.audiencia_id', '=', 'audiencias.id')
                ->join('expedientes', 'audiencias.expediente_id', '=', 'expedientes.id')
                ->where("agenda_audiencias.conciliador_id","=",$conciliador->id)
                ->where("audiencias.fecha_audiencia",">",date("Y-m-d"))
                ->select("audiencias.*","expedientes.folio","expedientes.anio","agenda_audiencias.id as agenda_audiencias_id","salas.sala")
                ->get();
//        dd($audiencias);
        $arrayResponse = array();
        foreach($audiencias as $audiencia){
            $start = $audiencia->fecha_audiencia ." ". $audiencia->hora_inicio;
            $end = $audiencia->fecha_audiencia ." ". $audiencia->hora_fin;
            $audiencia->partes = $this->GetPartesConciliador($audiencia->id);
            array_push($arrayResponse,array(
                "start" => $start ,
                "end" => $end,
                "title" => $audiencia->folio."/".$audiencia->anio,
                "color" => "#00ACAC",
                "audiencia" => $audiencia,
            ));
        }
        return $arrayResponse;
    }
    private function GetPartesConciliador($audiencia_id){
//         obtenemos la audiencia
        $audiencia = Audiencia::find($audiencia_id);
//         buscamos las partes
        $partesAudiencia=DB::table('partes')
                ->join('tipo_partes',"partes.tipo_parte_id","=","tipo_partes.id")
                ->join('solicitudes',"partes.solicitud_id","=","solicitudes.id")
                ->join('expedientes',"solicitudes.id","=","expedientes.solicitud_id")
                ->where("expedientes.id","=",$audiencia->expediente_id)
                ->select("partes.*","tipo_partes.nombre as tipo_parte")
                ->get();
//         validamos si la audiencia se celebrará en conjunto o separados
//         Recorremos las partes
        foreach($partesAudiencia as $key => $parte){
//                Buscamos a los conciliadores
            $conciliador = DB::table("agenda_audiencias")
                    ->join("conciliadores","agenda_audiencias.conciliador_id","=","conciliadores.id")
                    ->join("personas","conciliadores.persona_id","=","personas.id")
                    ->join("salas","agenda_audiencias.sala_id","=","salas.id")
                    ->where("agenda_audiencias.audiencia_id","=",$audiencia->id)
                    ->select("personas.*","agenda_audiencias.*","salas.sala")
                    ->get();
            if(!$audiencia->multiple || $audiencia->multiple == null){
//                  Asignamos el unico conciliador               
                $partesAudiencia[$key]->nombreConciliador = $conciliador[0]->nombre." ".$conciliador[0]->primer_apellido." ".$conciliador[0]->segundo_apellido;
                $partesAudiencia[$key]->sala = $conciliador[0]->sala;
            }else{
//                si la parte es el solicinte asignamos al responsable
                if($parte->tipo_parte_id == 1){
                    foreach($conciliador as $key2 => $value){
                        if($value->solicitante){
                            $partesAudiencia[$key]->nombreConciliador = $value->nombre." ".$value->primer_apellido."".$value->segundo_apellido;
                            $partesAudiencia[$key]->sala = $value->sala;
                        }
                    }
                }else{
                    foreach($conciliador as $key2 => $value){
                        if(!$value->solicitante){
                            $partesAudiencia[$key]->nombreConciliador = $value->nombre." ".$value->primer_apellido."".$value->segundo_apellido;
                            $partesAudiencia[$key]->sala = $value->sala;
                        }
                    }
                }
            }
        }
        return $partesAudiencia;
    }
}
