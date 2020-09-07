<?php

namespace App\Http\Controllers;

use App\Traits\GenerateDocument;
use Illuminate\Http\Request;
use App\Audiencia;
use App\Conciliador;
use App\Sala;
use App\EntidadEmisora;
use App\ConciliadorAudiencia;
use App\SalaAudiencia;
use App\Centro;
use App\Parte;
use App\Compareciente;
use App\TipoParte;
use App\AudienciaParte;
use App\EtapaResolucionAudiencia;
use App\ConceptoPagoResolucion;
use App\EtapaResolucion;
use App\Events\GenerateDocumentResolution;
use App\Expediente;
use App\ResolucionPartes;
use App\Resolucion;
use App\MotivoArchivado;
use Illuminate\Support\Facades\App;
use Validator;
use App\Filters\CatalogoFilter;
use App\GiroComercial;
use App\Jornada;
use App\Ocupacion;
use App\Periodicidad;
use App\ClasificacionArchivo;
use App\ResolucionParteConcepto;
use App\TerminacionBilateral;
use App\Traits\ValidateRange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AudienciaController extends Controller
{
    use ValidateRange;
    protected $request;

    public function __construct(Request $request)
    {
        if(!$request->is("*buzon/*")){
            $this->middleware('auth');
        }
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
                $audiencias->where('fecha_audiencia',"=",$this->request->get('fechaAudiencia') )->orderBy("fecha_audiencia",'desc');
                // $audiencias->where('fecha_audiencia',">",$this->request->get('fechaAudiencia') . $limInf);
            }
            if($this->request->get('NoAudiencia')){
                $audiencias->where('numero_audiencia',$this->request->get('NoAudiencia'))->orderBy("fecha_audiencia",'desc');
            }
            if($this->request->get('estatus_audiencia')){
                if($this->request->get('estatus_audiencia') == 2){
                    $audiencias->where('finalizada',true);
                    $date = Carbon::now();
                    $audiencias->where('fecha_audiencia',"<=",$date)->orderBy('fecha_audiencia','desc');
                }else if($this->request->get('estatus_audiencia') == 1){
                    $audiencias->where('finalizada',false);
                    $date = Carbon::now();
                    $audiencias->where('fecha_audiencia',">=",$date)->orderBy('fecha_audiencia','asc');
                }
            }
            if($this->request->get('expediente_id') != ""){
                $audiencias->where('expediente_id',"=",$this->request->get('expediente_id'))->orderBy('fecha_audiencia','asc');
            }
            if($this->request->get('IsDatatableScroll')){
                $audiencias = $audiencias->with('conciliador.persona');
                $audiencias = $audiencias->orderBy("fecha_audiencia", 'desc')->take($length)->skip($start)->get(['id','folio','anio','fecha_audiencia','hora_inicio','hora_fin','conciliador_id','finalizada']);
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
        return view('expediente.audiencias.index');
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
            $parte->parte->tipo_notificacion = $parte->tipo_notificacion;
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
        $concepto_pago_resoluciones = ConceptoPagoResolucion::all();
        $periodicidades = $this->cacheModel('periodicidades',Periodicidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones',Ocupacion::class);
        $jornadas = $this->cacheModel('jornadas',Jornada::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales',GiroComercial::class);
        $clasificacion_archivos = $this->cacheModel('clasificacion_archivo',ClasificacionArchivo::class);
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        $etapa_resolucion = EtapaResolucion::orderBy('paso')->get();
        $resoluciones = $this->cacheModel('resoluciones',Resolucion::class);
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $concepto_pago_resoluciones = ConceptoPagoResolucion::all();

        $entidad = ClasificacionArchivo::find(1);
//        dd($entidad->entidad_emisora);

        if($audiencia->solictud_cancelcacion){
            $audiencia->justificante_id == null;
            foreach($audiencia->documentos as $documento){
                if($documento->clasificacion_archivo_id == 7){
                    $audiencia->justificante_id = $documento->id;
                }
            }
        }
        return view('expediente.audiencias.edit', compact('audiencia','etapa_resolucion','resoluciones','concepto_pago_resoluciones',"motivos_archivo","concepto_pago_resoluciones","periodicidades","ocupaciones","jornadas","giros_comerciales","clasificacion_archivos","clasificacion_archivos_Representante"));
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

        $conciliadores = Conciliador::where("centro_id", auth()->user()->centro_id)->get();
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
                        if($conciliadorAudiencia->audiencia->fecha_audiencia ==  $fechaInicioSola){
                            //Buscamos que la hora inicio no este entre una audiencia
                            $horaInicioAudiencia= $conciliadorAudiencia->audiencia->hora_inicio;
                            $horaFinAudiencia= $conciliadorAudiencia->audiencia->hora_fin;
                            $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
                        }
                    }
                }
            }
            if($pasa){
                $conciliador->persona = $conciliador->persona;
            }
                $conciliadoresResponse[]=$conciliador;
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
        $salas = Sala::where("centro_id", auth()->user()->centro_id)->get();
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
                        if($salaAudiencia->audiencia->fecha_audiencia ==  $fechaInicioSola){
                            //Buscamos que la hora inicio no este entre una audiencia
                            $horaInicioAudiencia= $salaAudiencia->audiencia->hora_inicio;
                            $horaFinAudiencia= $salaAudiencia->audiencia->hora_fin;
                            $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
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

        //Obtenemos el contador
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(3, auth()->user()->centro_id);

        //Se crea el registro de la audiencia
        $audiencia = Audiencia::create([
            "expediente_id" => $request->expediente_id,
            "multiple" => $multiple,
            "fecha_audiencia" => $request->fecha_audiencia,
            "hora_inicio" => $request->hora_inicio,
            "hora_fin" => $request->hora_fin,
            "conciliador_id" =>  1,
            "numero_audiencia" => 1,
            "reprogramada" => false,
            "anio" => $folio->anio,
            "folio" => $folio->contador
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
            $tipo_notificacion_id = null;
            foreach($request->listaNotificaciones as $notificaciones){
                if($notificaciones["parte_id"] == $parte->id){
                    $tipo_notificacion_id = $notificaciones["tipo_notificacion_id"];
                }
            }
            AudienciaParte::create(["audiencia_id" => $audiencia->id,"parte_id" => $parte->id,"tipo_notificacion_id" => $tipo_notificacion_id]);
        }
        $expediente = Expediente::find($request->expediente_id);
        //Se genera citatorio de audiencia
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,14,4));
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
        $centro = auth()->user()->centro;
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
        $arrayAudiencias=$this->getTodasAudiencias($centro->id);
        $ev=array_merge($arrayFechas,$arrayAudiencias);
        //construimos el arreglo general
        $arregloGeneral = array();
        $arregloGeneral["laboresCentro"] = $laboresCentro;
        $arregloGeneral["incidenciasCentro"] = $ev;
        $arregloGeneral["duracionPromedio"] = $centro->duracionAudiencia;
        //obtenemos el minmaxtime
        $minmax= $this->getMinMaxTime($centro);
        $arregloGeneral["minTime"] = $minmax["hora_inicio"];
        $arregloGeneral["maxtime"] = $minmax["hora_fin"];
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
        try{
            DB::beginTransaction();
            $audiencia = Audiencia::find($request->audiencia_id);
            if(!$audiencia->finalizada){

                if($request->timeline){
                    $audiencia->update(array("resolucion_id"=>$request->resolucion_id,"finalizada"=>true));
                }else{
                    $audiencia->update(array("convenio" => $request->convenio,"desahogo" => $request->desahogo,"resolucion_id"=>$request->resolucion_id,"finalizada"=>true));
                    foreach($request->comparecientes as $compareciente){
                        Compareciente::create(["parte_id" => $compareciente,"audiencia_id" => $audiencia->id,"presentado" => true]);
                    }
                }
                $this->guardarRelaciones($audiencia,$request->listaRelacion,$request->listaConceptos);
                $etapaAudiencia = EtapaResolucionAudiencia::create([
                    "etapa_resolucion_id"=>6,
                    "audiencia_id"=>$audiencia->id,
                    "evidencia"=>true
                ]);
                DB::commit();
            }
            return $audiencia;
        }catch(\Throwable $e){

            DB::rollback();
            dd($e);
            return $this->sendError('Error al registrar los comparecientes', 'Error');
        }
    }

    /**
     * Funcion para guardar las resoluciones individuales de las audiencias
     * @param Audiencia $audiencia
     * @param type $arrayRelaciones
     */
    public function guardarRelaciones(Audiencia $audiencia, $arrayRelaciones = array(), $listaConceptos = array() ){
        $partes = $audiencia->audienciaParte;
        $solicitantes = $this->getSolicitantes($audiencia);
        $solicitados = $this->getSolicitados($audiencia);
        foreach($solicitantes as $solicitante){
            foreach($solicitados as $solicitado){
                $bandera = true;
                if($arrayRelaciones != null){
                    foreach($arrayRelaciones as $relacion){
                        if($solicitante->parte_id == $relacion["parte_solicitante_id"] && $solicitado->parte_id == $relacion["parte_solicitado_id"]){
                            $terminacion = 3;
                            // se genera convenio
                            // event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,16,2,$solicitante->parte_id,$solicitado->parte_id));
                            $bandera = false;
                        }else{

                            $bandera = false;
                            $terminacion = 4;
                        }
                        $resolucionParte = ResolucionPartes::create([
                            "audiencia_id" => $audiencia->id,
                            "parte_solicitante_id" => $solicitante->parte_id,
                            "parte_solicitada_id" => $solicitado->parte_id,
                            "terminacion_bilateral_id" => $terminacion
                        ]);
                    }
                }
                if($bandera){
                    $terminacion = 1;
                    if($audiencia->resolucion_id == 3){
                        $terminacion = 5;
                        //se genera el acta de no conciliacion para todos los casos
                        event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,17,1,$solicitante->parte_id,$solicitado->parte_id));

                        $parte = $solicitado->parte;
                        if($parte->tipo_persona_id == 2){
                            $compareciente_parte = Parte::where("parte_representada_id",$parte->id)->first();
                            if($compareciente_parte != null){
                                $compareciente = Compareciente::where('parte_id',$compareciente_parte->id)->first();
                            }else{
                                $compareciente = null;
                            }
                        }else{
                            $compareciente = Compareciente::where('parte_id',$solicitado->parte_id)->first();
                        }
                        // Si no es compareciente se genera multa
                        if($compareciente == null){
                            // Se genera multa
                            event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,18,7,$solicitante->parte_id,$solicitado->parte_id));
                        }
                    }else if($audiencia->resolucion_id == 1){
                        $terminacion = 3;
                    }else if($audiencia->resolucion_id == 2){
                        $terminacion = 2;
                        // event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,16,2,$solicitante->parte_id,$solicitado->parte_id));
                    }
                    $resolucionParte = ResolucionPartes::create([
                        "audiencia_id" => $audiencia->id,
                        "parte_solicitante_id" => $solicitante->parte_id,
                        "parte_solicitada_id" => $solicitado->parte_id,
                        "terminacion_bilateral_id" => $terminacion
                    ]);
                }
                //guardar conceptos de pago para Convenio
                if($audiencia->resolucion_id == 1 && isset($resolucionParte) && $terminacion == 3 ){ //Hubo conciliacion
                // if($audiencia->resolucion_id == 1 ){ //Hubo conciliacion
                    if(isset($listaConceptos)){
                        if(count($listaConceptos) > 0){
                            foreach($listaConceptos as $key=>$conceptosSolicitante){//solicitantes
                                // foreach($conceptosSolicitante as $ke=>$conceptosPago){//conceptos por solicitante
                                    if($key == $solicitante->parte_id ){
                                        foreach($conceptosSolicitante as $k=>$concepto){
                                            ResolucionParteConcepto::create([
                                                "resolucion_partes_id" => $resolucionParte->id,
                                                "concepto_pago_resoluciones_id"=> $concepto["concepto_pago_resoluciones_id"],
                                                "dias"=>intval($concepto["dias"]),
                                                "monto"=>$concepto["monto"],
                                                "otro"=>$concepto["otro"]
                                            ]);
                                        }
                                    }
                                // }
                            }
                        }
                    }
                    if($terminacion == 3){
                        //Se genera el convenio
                        event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,16,2,$solicitante->parte_id,$solicitado->parte_id));
                    }
                }
            }
        }
        event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,15,3));
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
                $audienciaParte->parte->documentos;
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
        //Obtenemos el contador
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(3, auth()->user()->centro_id);
        ##creamos la resolución a partir de los datos ya existentes y los nuevos
        $audienciaN = Audiencia::create([
            "expediente_id" => $audiencia->expediente_id,
            "multiple" => $multiple,
            "fecha_audiencia" => $fecha_audiencia,
            "hora_inicio" => $hora_inicio,
            "hora_fin" => $hora_fin,
            "conciliador_id" =>  $audiencia->conciliador_id,
            "numero_audiencia" => 1,
            "reprogramada" => true,
            "anio" => $folio->anio,
            "folio" => $folio->contador
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
            $tipo_notificacion_id = 1;
            if($pasaSolicitante){
                $arregloPartesAgregadas[]=$relacion["parte_solicitante_id"];
                AudienciaParte::create(["audiencia_id" => $audienciaN->id,"parte_id" => $relacion["parte_solicitante_id"],"tipo_notificacion_id" => $tipo_notificacion_id ]);
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
        $expediente = Expediente::find($audiencia->expediente_id);
        //Se genera citatorio de audiencia
        event(new GenerateDocumentResolution($audiencia->id,$expediente->solicitud_id,14,4));
        return $audienciaN;
    }

    ############################### A partir de este punto comienzan las funciones para el chacklist ########################################
    public function AgendaConciliador(){
        return view('expediente.audiencias.agendaConciliador');
    }

    public function GetAudienciaConciliador(){
//        obtenemos los datos del conciliador
        $conciliador = auth()->user()->persona->conciliador;
//        obtenemos las audiencias programadas a partir de el día de hoy
        $arrayEventos=[];
        if($conciliador != null){
            $audiencias = $conciliador->ConciliadorAudiencia;
            foreach($audiencias as $audiencia){
                $start = $audiencia->audiencia->fecha_audiencia ." ". $audiencia->audiencia->hora_inicio;
                $end = $audiencia->audiencia->fecha_audiencia ." ". $audiencia->audiencia->hora_fin;
                array_push($arrayEventos,array("start" => $start ,"end" => $end,"title" => $audiencia->audiencia->folio."/".$audiencia->audiencia->anio,"color" => "#00ACAC","audiencia_id" => $audiencia->audiencia->id));
            }
        }
        // obtenemos el horario menor y mayor del conciliador
        $maxMinDisponibilidad = $this->getMinMaxConciliador($conciliador);
        $response = array("eventos" => $arrayEventos,"minTime"=>$maxMinDisponibilidad["hora_inicio"],"maxTime"=>$maxMinDisponibilidad["hora_fin"]  );
        return $response;
    }
    public function guiaAudiencia($id){
        $etapa_resolucion = EtapaResolucion::orderBy('paso')->get();
        $audiencia = Audiencia::find($id);
        $partes = array();
        foreach($audiencia->audienciaParte as $key => $parte){
            $parte->parte->tipoParte = $parte->parte->tipoParte;
            $partes[$key] = $parte->parte;
        }
        $solicitud_id = $audiencia->expediente->solicitud->id;
        $audiencia->partes = $partes;
        $periodicidades = $this->cacheModel('periodicidades',Periodicidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones',Ocupacion::class);
        $jornadas = $this->cacheModel('jornadas',Jornada::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales',GiroComercial::class);
        $resoluciones = $this->cacheModel('resoluciones',Resolucion::class);
        $terminacion_bilaterales = $this->cacheModel('terminacion_bilaterales',TerminacionBilateral::class);
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $motivos_archivo = MotivoArchivado::all();
        $concepto_pago_resoluciones = ConceptoPagoResolucion::where('id','<=',9)->get();
        $concepto_pago_reinstalacion = ConceptoPagoResolucion::whereIn('id',[8,9,10])->get();
        $clasificacion_archivo = ClasificacionArchivo::all();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        return view('expediente.audiencias.etapa_resolucion',compact('etapa_resolucion','audiencia','periodicidades','ocupaciones','jornadas','giros_comerciales','resoluciones','concepto_pago_resoluciones','concepto_pago_reinstalacion','motivos_archivo','clasificacion_archivos_Representante','clasificacion_archivo','terminacion_bilaterales','solicitud_id'));
    }
    public function resolucionUnica($id){
        $etapa_resolucion = EtapaResolucion::orderBy('paso')->get();
        $audiencia = Audiencia::find($id);
        $partes = array();
        foreach($audiencia->audienciaParte as $key => $parte){
            $parte->parte->tipoParte = $parte->parte->tipoParte;
            $partes[$key] = $parte->parte;
        }
        $solicitud = $audiencia->expediente->solicitud;
        $audiencia->partes = $partes;
        $periodicidades = $this->cacheModel('periodicidades',Periodicidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones',Ocupacion::class);
        $jornadas = $this->cacheModel('jornadas',Jornada::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales',GiroComercial::class);
        $resoluciones = $this->cacheModel('resoluciones',Resolucion::class);
        $terminacion_bilaterales = $this->cacheModel('terminacion_bilaterales',TerminacionBilateral::class);
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $motivos_archivo = MotivoArchivado::all();
        $concepto_pago_resoluciones = ConceptoPagoResolucion::all();
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        return view('expediente.audiencias.resolucion_unica',compact('etapa_resolucion','audiencia','periodicidades','ocupaciones','jornadas','giros_comerciales','resoluciones','concepto_pago_resoluciones','motivos_archivo','clasificacion_archivos_Representante','clasificacion_archivo','terminacion_bilaterales','solicitud'));
    }
    public function guardarComparecientes(){
        DB::beginTransaction();
        try{
            $solicitantes = false;
            $citados = false;
            $audiencia = Audiencia::find($this->request->audiencia_id);
            if(!$audiencia->finalizada){

                foreach($this->request->comparecientes as $compareciente){
                    $parte_compareciente = Parte::find($compareciente);
                    if($parte_compareciente->tipo_parte_id == 1){
                        $solicitantes = true;
                    }else if($parte_compareciente->tipo_parte_id == 2){
                        $citados = true;
                    }else if($parte_compareciente->tipo_parte_id == 3){
                        $parte_representada = Parte::find($parte_compareciente->parte_representada_id);
                        if($parte_representada->tipo_parte_id == 1){
                            $solicitantes = true;
                        }else if($parte_representada->tipo_parte_id == 2){
                            $citados = true;
                        }
                    }

                    Compareciente::create(["parte_id" => $compareciente,"audiencia_id" => $this->request->audiencia_id,"presentado" => true]);
                }
                if(!$solicitantes){
                    //Archivado y se genera formato de acta de archivado por no comparecencia

                    $audiencia->update(array("resolucion_id"=>4,"finalizada"=>true));
                    $solicitantes = $this->getSolicitantes($audiencia);
                    $solicitados = $this->getSolicitados($audiencia);
                    foreach($solicitantes as $solicitante){
                        foreach($solicitados as $solicitado){
                            $resolucionParte = ResolucionPartes::create([
                                "audiencia_id" => $audiencia->id,
                                "parte_solicitante_id" => $solicitante->parte_id,
                                "parte_solicitada_id" => $solicitado->parte_id,
                                "terminacion_bilateral_id" => 1
                                ]);
                            }
                        }
                        // Se genera archivo de acta de archivado
                        event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,1,1));

                    }
                    if($solicitantes && !$citados){
                        //Constancia de no conciliacion de todos los citados
                        $audiencia->update(array("resolucion_id"=>3,"finalizada"=>true));
                        $solicitantes = $this->getSolicitantes($audiencia);
                        $solicitados = $this->getSolicitados($audiencia);
                        foreach($solicitantes as $solicitante){
                            foreach($solicitados as $solicitado){
                                $resolucionParte = ResolucionPartes::create([
                                    "audiencia_id" => $audiencia->id,
                                    "parte_solicitante_id" => $solicitante->parte_id,
                                    "parte_solicitada_id" => $solicitado->parte_id,
                                    "terminacion_bilateral_id" => 5
                                ]);
                                //Se genera constancia de no conciliacion
                                event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,17,1,$solicitante->parte_id,$solicitado->parte_id));

                                // Se genera archivo de acta de multa
                                event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,18,7,$solicitante->parte_id,$solicitado->parte_id));
                            }
                        }
                    }
            }
            DB::commit();
            return $this->sendResponse($audiencia, 'SUCCESS');
        }catch(\Throwable $e){
            DB::rollback();
            return $this->sendError('Error al registrar los comparecientes'.$e->getMessage(), 'Error');
        }
    }
    public function getComparecientes(){
        $audiencia = Audiencia::find($this->request->audiencia_id);
        $comparecientes = array();
        foreach($audiencia->comparecientes as $key=>$compareciente){
            $compareciente->parte = $compareciente->parte;
            $compareciente->documentos = $compareciente->parte->documentos;
            $parteRep=[];
            if($compareciente->parte->tipo_parte_id == 3 && $compareciente->parte->parte_representada_id != null){
                $parteRep = Parte::find($compareciente->parte->parte_representada_id);
            }
            $compareciente->parte->parteRepresentada = $parteRep;
            $compareciente->parte->tipoParte = TipoParte::find($compareciente->parte->tipo_parte_id)->nombre;
            $comparecientes[$key] = $compareciente;
        }
        return $comparecientes;
    }

    public function uploadJustificante(Request $request){
        DB::beginTransaction();
        try{
            $audiencia = Audiencia::find($request->audiencia_id);
            $audiencia->update(["solictud_cancelcacion" => true]);
            $directorio = 'expedientes/'.$audiencia->expediente_id.'/audiencias/'.$request->audiencia_id;
            Storage::makeDirectory($directorio);
            $archivo = $request->file('justificante');
            $tipoArchivo = ClasificacionArchivo::find(7);
//            DB::rollback();
//            dd($archivos);
//            foreach($archivos as $archivo) {
                $path = $archivo->store($directorio);
                $audiencia->documentos()->create([
                    "nombre" => str_replace($directorio."/", '',$path),
                    "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                    "descripcion" => "Justificante ".$tipoArchivo->nombre,
                    "ruta" => $path,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => 7 ,
                ]);
//            }
            DB::commit();
            return redirect()->back()->with('success', 'Se solicitó la cancelación');
        } catch (\Throwable $e) {
            dd($e);
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error');
            }
            return redirect()->back()->with('error', 'No se pudo solicitar la cancelación');
        }

    }

     /**
     * Función para almacenar catalogos (nombre,id) en cache
     *
     * @param [string] $nombre
     * @param [Model] $modelo
     * @return void
     */
    public function cacheModel($nombre,$modelo){
        if (!Cache::has($nombre)) {
            $respuesta = array_pluck($modelo::all(),'nombre','id');
            Cache::forever($nombre, $respuesta);
        }else{
            $respuesta = Cache::get($nombre);
        }
        return $respuesta;
    }
    public function negarCancelacion(){
        $audiencia = Audiencia::find($this->request->audiencia_id);
        $audiencia->update(["cancelacion_atendida" => true]);
        return $audiencia;
    }
    public function cambiarFecha() {
        try{
            DB::beginTransaction();
            $audiencia = Audiencia::find($this->request->audiencia_id);
            $audiencia->update(["fecha_audiencia" => $this->request->fecha_audiencia,"hora_inicio" => $this->request->hora_inicio,"hora_fin" => $this->request->hora_fin,"cancelacion_atendida" => true]);
            //Se genera citatorio de audiencia
            event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,14,4));
            DB::commit();
            return $audiencia;
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->sendError('Algo salio mal al tratar de reagendar', 'Error');
        }
    }
    public function getMinMaxTime(Centro $centro){
//        Recorremos las disponibilidades
        $horaIniciopiv = "23:59:59";
        $horaFinpiv = "00:00:00";
        foreach($centro->disponibilidades as $disponibilidad){
            if($disponibilidad->hora_inicio < $horaIniciopiv){
                $horaIniciopiv = $disponibilidad->hora_inicio;
            }
            if($disponibilidad->hora_fin > $horaIniciopiv){
                $horaFinpiv = $disponibilidad->hora_fin;
            }
        }
        return array("hora_inicio" => $horaIniciopiv, "hora_fin" =>$horaFinpiv);
    }
    public function getMinMaxConciliador(Conciliador $conciliador){
//        Recorremos las disponibilidades
        $horaIniciopiv = "23:59:59";
        $horaFinpiv = "00:00:00";
        foreach($conciliador->disponibilidades as $disponibilidad){
            if($disponibilidad->hora_inicio < $horaIniciopiv){
                $horaIniciopiv = $disponibilidad->hora_inicio;
            }
            if($disponibilidad->hora_fin > $horaIniciopiv){
                $horaFinpiv = $disponibilidad->hora_fin;
            }
        }
        return array("hora_inicio" => $horaIniciopiv, "hora_fin" =>$horaFinpiv);
    }

    use GenerateDocument;
    public function debug()
    {


        $pdf = App::make('snappy.pdf.wrapper');


        //$html = $this->generarConstancia(12, 12, 16,2, 23, 24, null);
        $html = $this->generarConstancia(12, 12, 15,3, null, null, null);
        $pdf->loadHTML($html);
        return $pdf->inline();

        return $html;
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->stream();


    }
}
