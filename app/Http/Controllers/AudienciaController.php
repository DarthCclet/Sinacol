<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Audiencia;
use App\Conciliador;
use App\Sala;
use App\ConciliadorAudiencia;
use App\SalaAudiencia;
use App\Centro;
use App\Parte;
use App\Compareciente;
use App\TipoParte;
use App\AudienciaParte;
use App\ResolucionPartes;
use App\Resolucion;
use App\MotivoArchivado;
use Validator;
use App\Filters\CatalogoFilter;
use Illuminate\Support\Facades\DB;
use App\Traits\ValidateRange;
use App\Traits\GenerateDocument;

class AudienciaController extends Controller
{
    use GenerateDocument;
    use ValidateRange;
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
            ->filter(false);

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all') ) {
            $audiencias = $audiencias->get();
        } else {
            $length = $this->request->get('length');
            $start = $this->request->get('start');
            $limSup = " 23:59:59";
            $limInf = " 00:00:00";
            if($this->request->get('fechaAudiencia')){
                $audiencias->where('fecha_audiencia',"=",$this->request->get('fechaAudiencia') );
                // $audiencias->where('fecha_audiencia',">",$this->request->get('fechaAudiencia') . $limInf);     
            }
            if($this->request->get('NoAudiencia')){
                $audiencias->where('numero_audiencia',$this->request->get('NoAudiencia'));
            }
            if($this->request->get('IsDatatableScroll')){
                $audiencias = $audiencias->with('conciliador.persona');
                $audiencias = $audiencias->orderBy("fecha_audiencia",'desc')->take($length)->skip($start)->get();
                // $audiencias = $audiencias->select(['id','conciliador','numero_audiencia','fecha_audiencia','hora_inicio','hora_fin'])->orderBy("fecha_audiencia",'desc')->take($length)->skip($start)->get();
            }else{
                $audiencias = $audiencias->paginate($this->request->get('per_page', 10));
            }
            
        }
        // // Para cada objeto obtenido cargamos sus relaciones.
        $audiencias = tap($audiencias)->each(function ($audiencia) {
            $audiencia->loadDataFromRequest();
        });

        // return $this->sendResponse($solicitud, 'SUCCESS');

        if ($this->request->wantsJson()) {
            if ($this->request->get('all') || $this->request->get('paginate') ) {
                return $this->sendResponse($audiencias, 'SUCCESS');
            }else{
                $total = Audiencia::count();
                $draw = $this->request->get('draw');
                return $this->sendResponseDatatable($total,$total,$draw,$audiencias, null);
            }
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
        $partes = array();
        $conciliadores = array();
        $salas = array();
        $comparecientes = array();
        foreach($audiencia->audienciaParte as $key => $parte){
            $parte->parte->tipoParte = $parte->parte->tipoParte;
            $partes[$key] = $parte->parte;
        }
        foreach($audiencia->conciliadoresAudiencias as $key => $conciliador){
            $conciliador->conciliador->persona = $conciliador->conciliador->persona;
            $conciliadores[$key] = $conciliador;
        }
        foreach($audiencia->salasAudiencias as $key => $sala){
            $sala->sala = $sala->sala;
            $salas[$key] = $sala;
        }
        foreach($audiencia->comparecientes as $key=>$compareciente){
            $compareciente->parte = $compareciente->parte;
            $parteRep=[];
            if($compareciente->parte->tipo_parte_id == 3 && $compareciente->parte->parte_representada_id != null){
                $parteRep = Parte::find($compareciente->parte->parte_representada_id);
            }
            $compareciente->parte->parteRepresentada = $parteRep;
            $comparecientes[$key] = $compareciente;
        }
        $audiencia->resolucionPartes = $audiencia->resolucionPartes;
        $audiencia->comparecientes = $comparecientes;
        $audiencia->partes = $partes;
        $audiencia->conciliadores = $conciliadores;
        $audiencia->salas = $salas;
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $motivos_archivo = MotivoArchivado::all();
        return view('expediente.audiencias.edit', compact('audiencia',"motivos_archivo"));
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
        $fechaInicioSola = date('Y-m-d',strtotime($request->fechaInicio));
        $horaInicio = date('H:i:s',strtotime($request->fechaInicio));
        $diaSemana = date('N',strtotime($request->fechaInicio));
        $fechaFin = $request->fechaFin;
        $fechaFinSola = date('Y-m-d',strtotime($request->fechaFin));
        $horaFin = date('H:i:s',strtotime($request->fechaFin));
//        dd($horaInicio);
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
                if($pasa){
                    $conciliadoresAudiencia = array();
                    foreach($conciliador->conciliadorAudiencia as $conciliadorAudiencia){
                        $audiencias = $conciliadorAudiencia->audiencia->where("fecha_audiencia",$fechaInicioSola)->get();
                        if(count($audiencias) > 0){
                            foreach($audiencias as $audiencia){
                                //Buscamos que la hora inicio no este entre una audiencia
                                $horaInicioAudiencia= $audiencia->hora_inicio;
                                $horaFinAudiencia= $audiencia->hora_fin;
                                $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
                            }
                        }
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
    /**
     * Funcion para obtener las Salas disponibles
     * @param Request $request
     * @return type
     */
    public function SalasDisponibles(Request $request)
    {
        ## Agregamos las variables con lo que recibimos
        $fechaInicio = $request->fechaInicio;
        $fechaInicioSola = date('Y-m-d',strtotime($request->fechaInicio));
        $horaInicio = date('H:i:s',strtotime($request->fechaInicio));
        $diaSemana = date('N',strtotime($request->fechaInicio));
        $fechaFin = $request->fechaFin;
        $fechaFinSola = date('Y-m-d',strtotime($request->fechaFin));
        $horaFin = date('H:i:s',strtotime($request->fechaFin));
        ## Obtenemos las salas -> en el futuro seran filtradas por el centro de la sesión
        $salas = Sala::all();
        $salasResponse=[];
        ## Recorremos las salas para la audiencia
        foreach($salas as $sala){
            $pasa=false;
            ## buscamos si tiene disponibilidad y si esta en el día que se solicita
            if(count($sala->disponibilidades) > 0){
                foreach($sala->disponibilidades as $disp){
                    if($disp["dia"] == $diaSemana){
                        $pasa = true;
                    }
                }
            }else{$pasa=false;}
            if($pasa){
                ## Validamos que no haya incidencias
                foreach($sala->incidencias as $inci){
                    if($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]){
                        $pasa=false;
                    }
                }
                if($pasa){
                    ## validamos que no haya audiencias en el horario solicitado
                    foreach($sala->salaAudiencia as $salaAudiencia){
                        $audiencias = $salaAudiencia->audiencia->where("fecha_audiencia",$fechaInicioSola)->get();
                        if(count($audiencias) > 0){
                            foreach($audiencias as $audiencia){
                                //Buscamos que la hora inicio no este entre una audiencia
                                $horaInicioAudiencia= $audiencia->hora_inicio;
                                $horaFinAudiencia= $audiencia->hora_fin;
                                $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
                            }
                        }
                    }
                }
            }
            
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
        if($request->tipoAsignacion == 1){
            $multiple = false;
        }else{
            $multiple = true;

        }
        //Se crea el registro de la audiencia
        $audiencia = Audiencia::create([
            "expediente_id" => $request->expediente_id,
            "multiple" => $multiple,
            "fecha_audiencia" => $request->fecha_audiencia,
            "hora_inicio" => $request->hora_inicio, 
            "hora_fin" => $request->hora_fin,
            "conciliador_id" =>  1,
            "numero_audiencia" => 1,
            "reprogramada" => false
        ]);
        $id_conciliador = null;
        foreach ($request->asignacion as $value) {
            if($value["resolucion"]){
                $id_conciliador = $value["conciliador"];
            }
            ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $value["conciliador"],"solicitante" => $value["resolucion"]]);
            SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $value["sala"],"solicitante" => $value["resolucion"]]);
        }
        $audiencia->update(["conciliador_id" => $id_conciliador]);
        
        // Guardamos todas las Partes en la audiencia
        $partes = $audiencia->expediente->solicitud->partes;
        foreach($partes as $parte){
            AudienciaParte::create(["audiencia_id" => $audiencia->id,"parte_id" => $parte->id]);
        }
        
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
        $Audiencias = Audiencia::where("fecha_audiencia",">=",date("Y-m-d"))->get();
        foreach($Audiencias as $Audiencia){
            $Audiencia->folio = $Audiencia->expediente->folio;
            $Audiencia->anio = $Audiencia->expediente->anio;
        }
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
        $audiencia->update(array("convenio" => $request->convenio,"desahogo" => $request->desahogo,"resolucion_id"=>$request->resolucion_id,"finalizada"=>true));
        foreach($request->comparecientes as $compareciente){
            Compareciente::create(["parte_id" => $compareciente,"audiencia_id" => $audiencia->id,"presentado" => true]);
        }
        $this->guardarRelaciones($audiencia,$request->listaRelacion);
        return $audiencia;
    }
    
    /**
     * Funcion para guardar las resoluciones individuales de las audiencias
     * @param Audiencia $audiencia
     * @param type $arrayRelaciones
     */
    public function guardarRelaciones(Audiencia $audiencia, $arrayRelaciones = array()){
//        dd($arrayRelaciones);
        $partes = $audiencia->audienciaParte;
        $solicitantes = $this->getSolicitantes($audiencia);
        $solicitados = $this->getSolicitados($audiencia);
        foreach($solicitantes as $solicitante){
            foreach($solicitados as $solicitado){
                $bandera = true;
                foreach($arrayRelaciones as $relacion){
                    if($solicitante->parte_id == $relacion["parte_solicitante_id"] && $solicitado->parte_id == $relacion["parte_solicitado_id"]){
                        ResolucionPartes::create([
                            "audiencia_id" => $audiencia->id,
                            "parte_solicitante_id" => $solicitante->parte_id,
                            "parte_solicitada_id" => $solicitado->parte_id,
                            "resolucion_id" => $relacion["resolucion_individual_id"]
                        ]);
                        $bandera = false;
                    }
                }
                if($relacion["resolucion_individual_id"] == 3){
                    $this->generarConstancia($audiencia->id,$audiencia->expediente->solicitud->id,1,1,$solicitante->parte_id,$solicitado->parte_id);
                }
                if($bandera){
                    ResolucionPartes::create([
                        "audiencia_id" => $audiencia->id,
                        "parte_solicitante_id" => $solicitante->parte_id,
                        "parte_solicitada_id" => $solicitado->parte_id,
                        "resolucion_id" => $audiencia->resolucion_id
                    ]);
                }
            }
        }
    }
    /**
     * Funcion para obtener los documentos de la audiencia
     * @param type $audiencia_id
     * @return type
     */
    function getDocumentosAudiencia($audiencia_id){
        $audiencia = Audiencia::find($audiencia_id);
        $documentos = $audiencia->documentos;
        foreach($documentos as $documento){
            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
            $documento->tipo = pathinfo($documento->ruta)['extension'];
        }
        return $documentos;
    }
    
    /**
     * Funcion para obtener todas las personas fisicas
     * @param int $audiencia_id
     * @return array Partes $partes
     */
    public function GetPartesFisicas($audiencia_id){
        $audiencia = Audiencia::find($audiencia_id);
//        dd($audiencia->expediente->solicitud->partes);
        $partes=[];
        foreach($audiencia->audienciaParte as $audienciaParte){
            if($audienciaParte->parte->tipo_persona_id == 1){
                $audienciaParte->parte->tipoParte = $audienciaParte->parte->tipoParte;
                $partes[] = $audienciaParte->parte;
            }
        }
        return $partes;
    }
    
    /**
     * Función para validar si se puede dar resolución a una audiencia
     * @param int $audiencia_id
     * @return boolean
     */
    public function validarPartes($audiencia_id){
        // Obtenemos las partes de la audiencia que sean de tipo persona moral;
        $audiencia = Audiencia::find($audiencia_id);
        $partes = $audiencia->expediente->solicitud->partes->where("tipo_persona_id","2");
        // Validamos si hay personas morales
        if(count($partes)){
            foreach($partes as $parte){
                $representante = Parte::where("parte_representada_id",$parte->id)->get();
                if(!count($representante)){
                    // Si la persona moral no tiene representante no se puede guardar
                    return ["pasa" => false];
                }
            }
            // Si siempre tiene representante 
            return ["pasa" => true];
        }else{
            // Si no hay personas Morales se puede guardar la resolución
            return ["pasa" => true];
        }
    }
    
    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitante
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitante
     */
    public function getSolicitantes(Audiencia $audiencia){
        $solicitantes = [];
        foreach($audiencia->audienciaParte as $parte){
            if($parte->parte->tipo_parte_id == 1){
                $solicitantes[]=$parte;
            }
        }
        return $solicitantes;
    }
    
    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitado
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitado
     */
    public function getSolicitados(Audiencia $audiencia){
        $solicitados = [];
        foreach($audiencia->audienciaParte as $parte){
            if($parte->parte->tipo_parte_id == 2){
                $solicitados[]=$parte;
            }
        }
        return $solicitados;
    }
    
    public function NuevaAudiencia(Request $request){
        ##Obtenemos la audiencia origen
        $audiencia = Audiencia::find($request->audiencia_id);
        
        ## Validamos si la audiencia se calendariza o solo es para guardar una resolución distinta
        if($request->nuevaCalendarizacion == "S"){
            $fecha_audiencia = $request->fecha_audiencia;
            $hora_inicio = $request->hora_inicio;
            $hora_fin = $request->hora_fin;
            if($request->tipoAsignacion == 1){
                $multiple = false;
            }else{
                $multiple = true;
            }
        }else{
            $fecha_audiencia = $audiencia->fecha_audiencia;
            $hora_inicio = $audiencia->hora_inicio;
            $hora_fin = $audiencia->hora_fin;
            $multiple = $audiencia->multiple;
        }
        ##creamos la resolución a partir de los datos ya existentes y los nuevos
        $audienciaN = Audiencia::create([
            "expediente_id" => $audiencia->expediente_id,
            "multiple" => $multiple,
            "fecha_audiencia" => $fecha_audiencia,
            "hora_inicio" => $hora_inicio, 
            "hora_fin" => $hora_fin,
            "conciliador_id" =>  $audiencia->conciliador_id,
            "numero_audiencia" => 1,
            "reprogramada" => true
        ]);
        ## si la audiencia se calendariza se deben guardar los datos recibidos en el arreglo, si no se copian los de la audiencia origen
        if($request->nuevaCalendarizacion == "S"){
            $id_conciliador = null;
            foreach ($request->asignacion as $value) {
                if($value["resolucion"]){
                    $id_conciliador = $value["conciliador"];
                }
                ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $value["conciliador"],"solicitante" => $value["resolucion"]]);
                SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $value["sala"],"solicitante" => $value["resolucion"]]);
            }
            $audienciaN->update(["conciliador_id" => $id_conciliador]);
        }else{
            foreach($audiencia->conciliadoresAudiencias as $conciliador){
                ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $conciliador->conciliador_id,"solicitante" => $conciliador->solicitante]);
            }
            foreach ($audiencia->salasAudiencias as $sala){
                SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $sala->sala_id,"solicitante" => $sala->solicitante]);
            }
        }
        
        ##Finalmente guardamos los datos de las partes recibidas
        $arregloPartesAgregadas = array();
        foreach($request->listaRelaciones as $relacion){
            ##Validamos que el solicitante no exista
            $pasaSolicitante = true;
            foreach($arregloPartesAgregadas as $arreglo){
                if($relacion["parte_solicitante_id"] == $arreglo){
                    $pasaSolicitante = false;
                }
            }
            if($pasaSolicitante){
                $arregloPartesAgregadas[]=$relacion["parte_solicitante_id"];
                AudienciaParte::create(["audiencia_id" => $audienciaN->id,"parte_id" => $relacion["parte_solicitante_id"]]);
            }
            ##Validamos que el solicitado no exista 
            $pasaSolicitado = true;
            foreach($arregloPartesAgregadas as $arreglo){
                if($relacion["parte_solicitada_id"] == $arreglo){
                    $pasaSolicitado = false;
                }
            }
            if($pasaSolicitado){
                $arregloPartesAgregadas[]=$relacion["parte_solicitada_id"];
                AudienciaParte::create(["audiencia_id" => $audienciaN->id,"parte_id" => $relacion["parte_solicitada_id"]]);
            }
            $resolucion = ResolucionPartes::find($relacion["id"]);
            $resolucion->update(["nuevaAudiencia" => true]);
        }
        return $audienciaN;
    }
}
