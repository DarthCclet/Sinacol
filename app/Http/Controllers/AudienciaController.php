<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Log;
use Validator;
use App\Filters\CatalogoFilter;
use App\GiroComercial;
use App\Jornada;
use App\Ocupacion;
use App\Periodicidad;
use App\Solicitud;
use App\ClasificacionArchivo;
use App\ResolucionPagoDiferido;
use App\ResolucionParteConcepto;
use App\TerminacionBilateral;
use App\Documento;
use App\Estado;
use App\TipoContacto;
use App\Traits\ValidateRange;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\FechaAudienciaService;
use Illuminate\Support\Facades\Auth;
use App\Traits\FechaNotificacion;
use App\Events\RatificacionRealizada;
use App\Genero;
use App\LenguaIndigena;
use App\Mail\CambioFecha;
use App\TipoAsentamiento;
use App\TipoVialidad;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AudienciaController extends Controller {

    use ValidateRange,FechaNotificacion;

    protected $request;

    public function __construct(Request $request) {
        if (!$request->is("*buzon/*")) {
            $this->middleware('auth');
        }
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
//        return Audiencia::all();
        Audiencia::with('conciliador')->get();
        // $solicitud = Solicitud::all();
        // Filtramos los usuarios con los parametros que vengan en el request
        $audiencias = (new CatalogoFilter(Audiencia::query(), $this->request))
                ->searchWith(Audiencia::class)
                ->filter(false);
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $audiencias = $audiencias->get();
        } else {

            $length = $this->request->get('length');
            $start = $this->request->get('start');
            $limSup = " 23:59:59";
            $limInf = " 00:00:00";
            if ($this->request->get('fechaAudiencia')) {
                $audiencias->where('fecha_audiencia', "=", $this->request->get('fechaAudiencia'))->orderBy("fecha_audiencia", 'desc');
                // $audiencias->where('fecha_audiencia',">",$this->request->get('fechaAudiencia') . $limInf);
            }
            if ($this->request->get('NoAudiencia')) {
                $audiencias->where('numero_audiencia', $this->request->get('NoAudiencia'))->orderBy("fecha_audiencia", 'desc');
            }
            if ($this->request->get('estatus_audiencia')) {
                if ($this->request->get('estatus_audiencia') == 2) {
                    $audiencias->where('finalizada', true);
                    // $date = Carbon::now();
                    // $audiencias->where('fecha_audiencia',"<=",$date)->orderBy('fecha_audiencia','desc');
                } else if ($this->request->get('estatus_audiencia') == 1) {
                    $audiencias->where('finalizada', false);
                    // $date = Carbon::now();
                    // $audiencias->where('fecha_audiencia',">=",$date)->orderBy('fecha_audiencia','asc');
                }else if($this->request->get('estatus_audiencia') == 3){
                    $audiencias->where("solictud_cancelcacion",true)->where("cancelacion_atendida",false);
                }
            }
            if ($this->request->get('expediente_id') != "") {
                $audiencias->where('expediente_id', "=", $this->request->get('expediente_id'))->orderBy('fecha_audiencia', 'asc');
            }
            
            $persona_id= Auth::user()->persona->id;
            $conciliador = Conciliador::where('persona_id',$persona_id)->first();
            if($conciliador != null){
                $conciliador_id = $conciliador->id;
                $audiencias->where('conciliador_id',$conciliador_id);
            }else{
                if ($this->request->wantsJson()) {
                    return $this->sendResponseDatatable(0, 0, 0, [], null);
                }
            }

            if ($this->request->get('IsDatatableScroll')) {
                $audiencias = $audiencias->with('conciliador.persona');
                $audiencias = $audiencias->with('expediente.solicitud');
                $audiencias = $audiencias->orderBy("fecha_audiencia", 'desc')->take($length)->skip($start)->get(['id', 'folio', 'anio', 'fecha_audiencia', 'hora_inicio', 'hora_fin', 'conciliador_id', 'finalizada','expediente_id','solictud_cancelcacion','cancelacion_atendida']);
                // $audiencias = $audiencias->select(['id','conciliador','numero_audiencia','fecha_audiencia','hora_inicio','hora_fin'])->orderBy("fecha_audiencia",'desc')->take($length)->skip($start)->get();
            } else {
                $audiencias = $audiencias->paginate($this->request->get('per_page', 10));
            }
        }
        // // Para cada objeto obtenido cargamos sus relaciones.
        $audiencias = tap($audiencias)->each(function ($audiencia) {
            $audiencia->loadDataFromRequest();
        });

        // return $this->sendResponse($solicitud, 'SUCCESS');

        if ($this->request->wantsJson()) {
            if ($this->request->get('all') || $this->request->get('paginate')) {
                return $this->sendResponse($audiencias, 'SUCCESS');
            } else {
                $total = Audiencia::count();
                $draw = $this->request->get('draw');
                $filtered = $audiencias->count();
                return $this->sendResponseDatatable($total, $filtered, $draw, $audiencias, null);
            }
        }
        $audiencias_reagendar = Audiencia::where("solictud_cancelcacion",true)->where("cancelacion_atendida",false)->get();
        $reagendar = count($audiencias_reagendar);
        return view('expediente.audiencias.index', compact('reagendar'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
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
    public function show($id) {
        return Audiencia::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Audiencia $audiencia) {
        $doc= collect();
        // obtenemos los conciliadores
        $partes = array();
        $conciliadores = array();
        $salas = array();
        $comparecientes = array();
        $conceptos_pago = array();
        $solicitantesComparecientes = array();

        foreach ($audiencia->audienciaParte as $key => $parte) {
            $parte->parte->tipoParte = $parte->parte->tipoParte;
            $parte->parte->tipo_notificacion = $parte->tipo_notificacion;
            $partes[$key] = $parte->parte;
        }
        foreach ($audiencia->conciliadoresAudiencias as $key => $conciliador) {
            $conciliador->conciliador->persona = $conciliador->conciliador->persona;
            $conciliadores[$key] = $conciliador;
        }
        foreach ($audiencia->salasAudiencias as $key => $sala) {
            $sala->sala = $sala->sala;
            $salas[$key] = $sala;
        }
        foreach ($audiencia->comparecientes as $key => $compareciente) {
            $compareciente->parte = $compareciente->parte;
            $parteRep = [];
            //representante legal
            if ($compareciente->parte->tipo_parte_id == 3 && $compareciente->parte->parte_representada_id != null) {
                $parteRep = Parte::find($compareciente->parte->parte_representada_id);
            }
            $compareciente->parte->parteRepresentada = $parteRep;
            $comparecientes[$key] = $compareciente;
            if ($compareciente->parte->tipo_parte_id == 1){
                $solicitantesComparecientes[$key] = $compareciente;
            }
        }
        $audiencia->solicitantesComparecientes = $solicitantesComparecientes;
        
        $audiencia->resolucionPartes = $audiencia->resolucionPartes;
        $audiencia->comparecientes = $comparecientes;
        $audiencia->partes = $partes;
        $audiencia->conciliadores = $conciliadores;
        $audiencia->salas = $salas;
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $motivos_archivo = MotivoArchivado::all();
        // $concepto_pago_resoluciones = ConceptoPagoResolucion::all();
        $audiencia->pagosDiferidos;
        // $audiencia->resolucionPartes->conceptoPagoResolucion;
        foreach ($audiencia->solicitantes as $audienciaParte) {
            $totalConceptos = 0;
            $conceptos =[];
            foreach ($audienciaParte->parteConceptos as $concepto){
                $totalConceptos += floatval($concepto->monto);
                $conceptosP = $concepto;
                $conceptosP->nombre = $concepto->ConceptoPagoResolucion->nombre;
                $conceptosP->idSolicitante = $audienciaParte->solicitante_id;
                array_push($conceptos,$conceptosP);   
            }
            array_push($conceptos_pago,['idSolicitante'=>$audienciaParte->parte_id, 'conceptos'=>$conceptos, 'totalConceptos'=>$totalConceptos]);
        }
        // foreach ($audiencia->resolucionPartes as $resolucionParte) {
        //     $totalConceptos = 0;
        //     $conceptos =[];
        //     foreach ($resolucionParte->parteConceptos as $concepto){
        //         $totalConceptos += floatval($concepto->monto);
        //         $conceptosP = $concepto;
        //         $conceptosP->nombre = $concepto->ConceptoPagoResolucion->nombre;
        //         $conceptosP->idSolicitante = $resolucionParte->parteSolicitante->id;
        //         array_push($conceptos,$conceptosP);   
        //     }
        //     array_push($conceptos_pago,['idSolicitante'=>$resolucionParte->parteSolicitante->id, 'conceptos'=>$conceptos, 'totalConceptos'=>$totalConceptos]);
        // }
        $periodicidades = $this->cacheModel('periodicidades', Periodicidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones', Ocupacion::class);
        $jornadas = $this->cacheModel('jornadas', Jornada::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales', GiroComercial::class);
        $clasificacion_archivos = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->get();
        $etapa_resolucion = EtapaResolucion::orderBy('paso')->get();
        $resoluciones = $this->cacheModel('resoluciones', Resolucion::class);
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $concepto_pago_resoluciones = ConceptoPagoResolucion::all();

        $entidad = ClasificacionArchivo::find(1);
//        dd($entidad->entidad_emisora);

        if ($audiencia->solictud_cancelcacion) {
            $audiencia->justificante_id == null;
            foreach ($audiencia->documentos as $documento) {
                if ($documento->clasificacion_archivo_id == 7) {
                    $audiencia->justificante_id = $documento->id;
                }
            }
        }

        $partes = array();
        $solicitud =$audiencia->expediente->solicitud;
        $solicitud_id = $solicitud->id;
        $estatus_solicitud_id = $solicitud->estatus_solicitud_id;
        $solicitudPartes = $solicitud->partes;
        foreach($solicitudPartes as $key => $parte){
            $documentos = $parte->documentos;
            foreach ($documentos as $documento) {
                $documento->id = $documento->id;
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta,PATHINFO_EXTENSION);
                $documento->parte = $parte->nombre. " ".$parte->primer_apellido." ".$parte->segundo_apellido;
                $documento->tipo_doc = 2;
                $doc->push($documento);
            }
        }
        $documentos = $solicitud->documentos;
        foreach ($documentos as $documento) {
            $documento->id = $documento->id;
            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
            $documento->tipo = pathinfo($documento->ruta,PATHINFO_EXTENSION);
            $documento->tipo_doc = 1;
            $doc->push($documento);
        }
        foreach($solicitud->expediente->audiencia as $audienciaSol){
            $documentos = $audienciaSol->documentos;

            foreach ($documentos as $documento) {
                $documento->id = $documento->id;
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta,PATHINFO_EXTENSION);
                $documento->tipo_doc = 3;
                $documento->audiencia = $audienciaSol->folio."/".$audienciaSol->anio;
                $documento->audiencia_id = $audienciaSol->id;
                $doc->push($documento);
            }
        }
        $documentos = $doc->sortBy('id');
        return view('expediente.audiencias.edit', compact('audiencia', 'etapa_resolucion', 'resoluciones', 'concepto_pago_resoluciones', "motivos_archivo", "conceptos_pago", "periodicidades", "ocupaciones", "jornadas", "giros_comerciales", "clasificacion_archivos", "clasificacion_archivos_Representante","documentos",'solicitud_id','estatus_solicitud_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
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
    public function destroy($id) {
        $audiencia = Audiencia::findOrFail($id)->delete();
        return 204;
    }

    /**
     * Muestra el calendario de las audiencias a celebrar
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function calendario() {
        return view('expediente.audiencias.calendario');
    }

    /**
     * Funcion para obtener los conciliadores disponibles
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ConciliadoresDisponibles(Request $request) {
        $fechaInicio = $request->fechaInicio;
        $fechaInicioSola = date('Y-m-d', strtotime($request->fechaInicio));
        $horaInicio = date('H:i:s', strtotime($request->fechaInicio));
        $diaSemana = date('N', strtotime($request->fechaInicio));
        $fechaFin = $request->fechaFin;
        $fechaFinSola = date('Y-m-d', strtotime($request->fechaFin));
        $horaFin = date('H:i:s', strtotime($request->fechaFin));

        $conciliadores = Conciliador::where("centro_id", auth()->user()->centro_id)->get();
        $conciliadoresResponse = [];
        foreach ($conciliadores as $conciliador) {
            $pasa = false;
            if (count($conciliador->disponibilidades) > 0) {
                foreach ($conciliador->disponibilidades as $disp) {
                    if ($disp["dia"] == $diaSemana) {
                        $pasa = true;
                    }
                }
//            } else {
//                $pasa = false;
            }
//            if ($pasa) {
//                foreach ($conciliador->incidencias as $inci) {
//                    if ($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]) {
//                        $pasa = false;
//                    }
//                }
//                if ($pasa) {
//                    $conciliadoresAudiencia = array();
//                    foreach ($conciliador->conciliadorAudiencia as $conciliadorAudiencia) {
//                        if ($conciliadorAudiencia->audiencia->fecha_audiencia == $fechaInicioSola) {
//                            //Buscamos que la hora inicio no este entre una audiencia
//                            $horaInicioAudiencia = $conciliadorAudiencia->audiencia->hora_inicio;
//                            $horaFinAudiencia = $conciliadorAudiencia->audiencia->hora_fin;
//                            $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
//                        }
//                    }
//                }
//            }
            if ($pasa) {
                $conciliador->persona = $conciliador->persona;
                $conciliadoresResponse[] = $conciliador;
            }
        }
        return $conciliadoresResponse;
    }
    /**
     * Funcion para obtener los conciliadores disponibles de la oficina central
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ConciliadoresDisponiblesCentral(Request $request) {
        $fechaInicio = $request->fechaInicio;
        $fechaInicioSola = date('Y-m-d', strtotime($request->fechaInicio));
        $horaInicio = date('H:i:s', strtotime($request->fechaInicio));
        $diaSemana = date('N', strtotime($request->fechaInicio));
        $fechaFin = $request->fechaFin;
        $fechaFinSola = date('Y-m-d', strtotime($request->fechaFin));
        $horaFin = date('H:i:s', strtotime($request->fechaFin));
        $centro = Centro::where("abreviatura","OCCFCRL")->first();

        $conciliadores = Conciliador::where("centro_id", $centro->id)->get();
        $conciliadoresResponse = [];
        foreach ($conciliadores as $conciliador) {
            $pasa = false;
            if (count($conciliador->disponibilidades) > 0) {
                foreach ($conciliador->disponibilidades as $disp) {
                    if ($disp["dia"] == $diaSemana) {
                        $pasa = true;
                    }
                }
            } else {
                $pasa = false;
            }
            if ($pasa) {
                foreach ($conciliador->incidencias as $inci) {
                    if ($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]) {
                        $pasa = false;
                    }
                }
                if ($pasa) {
                    $conciliadoresAudiencia = array();
                    foreach ($conciliador->conciliadorAudiencia as $conciliadorAudiencia) {
                        if ($conciliadorAudiencia->audiencia->fecha_audiencia == $fechaInicioSola) {
                            //Buscamos que la hora inicio no este entre una audiencia
                            $horaInicioAudiencia = $conciliadorAudiencia->audiencia->hora_inicio;
                            $horaFinAudiencia = $conciliadorAudiencia->audiencia->hora_fin;
                            $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
                        }
                    }
                }
            }
            if ($pasa) {
                $conciliador->persona = $conciliador->persona;
                $conciliadoresResponse[] = $conciliador;
            }
        }
        return $conciliadoresResponse;
    }

    /**
     * Funcion para obtener las Salas disponibles
     * @param Request $request
     * @return type
     */
    public function SalasDisponibles(Request $request) {
        ## Agregamos las variables con lo que recibimos
        $fechaInicio = $request->fechaInicio;
        $fechaInicioSola = date('Y-m-d', strtotime($request->fechaInicio));
        $horaInicio = date('H:i:s', strtotime($request->fechaInicio));
        $diaSemana = date('N', strtotime($request->fechaInicio));
        $fechaFin = $request->fechaFin;
        $fechaFinSola = date('Y-m-d', strtotime($request->fechaFin));
        $horaFin = date('H:i:s', strtotime($request->fechaFin));
        ## Obtenemos las salas -> en el futuro seran filtradas por el centro de la sesión
        $salas = Sala::where("centro_id", auth()->user()->centro_id)->get();
        $salasResponse = [];
        ## Recorremos las salas para la audiencia
        foreach ($salas as $sala) {
            $pasa = false;
            ## buscamos si tiene disponibilidad y si esta en el día que se solicita
            if (count($sala->disponibilidades) > 0) {
                foreach ($sala->disponibilidades as $disp) {
                    if ($disp["dia"] == $diaSemana) {
                        $pasa = true;
                    }
                }
            } else {
                $pasa = false;
            }
            if ($pasa) {
                ## Validamos que no haya incidencias
                foreach ($sala->incidencias as $inci) {
                    if ($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]) {
                        $pasa = false;
                    }
                }
                if ($pasa) {
                    ## validamos que no haya audiencias en el horario solicitado
                    foreach ($sala->salaAudiencia as $salaAudiencia) {
                        if ($salaAudiencia->audiencia->fecha_audiencia == $fechaInicioSola) {
                            //Buscamos que la hora inicio no este entre una audiencia
                            $horaInicioAudiencia = $salaAudiencia->audiencia->hora_inicio;
                            $horaFinAudiencia = $salaAudiencia->audiencia->hora_fin;
                            $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
                        }
                    }
                }
            }

            if ($pasa) {
                $salasResponse[] = $sala;
            }
        }
        return $salasResponse;
    }
    /**
     * Funcion para obtener las Salas disponibles de la oficina central
     * @param Request $request
     * @return type
     */
    public function SalasDisponiblesCentral(Request $request) {
        ## Agregamos las variables con lo que recibimos
        $fechaInicio = $request->fechaInicio;
        $fechaInicioSola = date('Y-m-d', strtotime($request->fechaInicio));
        $horaInicio = date('H:i:s', strtotime($request->fechaInicio));
        $diaSemana = date('N', strtotime($request->fechaInicio));
        $fechaFin = $request->fechaFin;
        $fechaFinSola = date('Y-m-d', strtotime($request->fechaFin));
        $horaFin = date('H:i:s', strtotime($request->fechaFin));
        ## Obtenemos las salas -> en el futuro seran filtradas por el centro de la sesión
        $centro = Centro::where("abreviatura","OCCFCRL")->first();
        $salas = Sala::where("centro_id", $centro->id)->get();
        $salasResponse = [];
        ## Recorremos las salas para la audiencia
        foreach ($salas as $sala) {
            $pasa = false;
            ## buscamos si tiene disponibilidad y si esta en el día que se solicita
            if (count($sala->disponibilidades) > 0) {
                foreach ($sala->disponibilidades as $disp) {
                    if ($disp["dia"] == $diaSemana) {
                        $pasa = true;
                    }
                }
            } else {
                $pasa = false;
            }
            if ($pasa) {
                ## Validamos que no haya incidencias
                foreach ($sala->incidencias as $inci) {
                    if ($fechaInicio >= $inci["fecha_inicio"] && $fechaFin <= $inci["fecha_fin"]) {
                        $pasa = false;
                    }
                }
                if ($pasa) {
                    ## validamos que no haya audiencias en el horario solicitado
                    foreach ($sala->salaAudiencia as $salaAudiencia) {
                        if ($salaAudiencia->audiencia->fecha_audiencia == $fechaInicioSola) {
                            //Buscamos que la hora inicio no este entre una audiencia
                            $horaInicioAudiencia = $salaAudiencia->audiencia->hora_inicio;
                            $horaFinAudiencia = $salaAudiencia->audiencia->hora_fin;
                            $pasa = $this->rangesNotOverlapOpen($horaInicioAudiencia, $horaFinAudiencia, $horaInicio, $horaFin);
                        }
                    }
                }
            }

            if ($pasa) {
                $salasResponse[] = $sala;
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
    public function calendarizar(Request $request) {
        if ($request->tipoAsignacion == 1) {
            $multiple = false;
        } else {
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
                    "conciliador_id" => 1,
                    "numero_audiencia" => 1,
                    "reprogramada" => false,
                    "anio" => $folio->anio,
                    "folio" => $folio->contador
        ]);
        $id_conciliador = null;
        foreach ($request->asignacion as $value) {
            if ($value["resolucion"]) {
                $id_conciliador = $value["conciliador"];
            }
            ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $value["conciliador"], "solicitante" => $value["resolucion"]]);
            SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $value["sala"], "solicitante" => $value["resolucion"]]);
        }
        $audiencia->update(["conciliador_id" => $id_conciliador]);

        // Guardamos todas las Partes en la audiencia
        $partes = $audiencia->expediente->solicitud->partes;
        $expediente = Expediente::find($request->expediente_id);
        foreach ($partes as $parte) {
            $tipo_notificacion_id = null;
            foreach ($request->listaNotificaciones as $notificaciones) {
                if ($notificaciones["parte_id"] == $parte->id) {
                    $tipo_notificacion_id = $notificaciones["tipo_notificacion_id"];
                }
            }
            AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parte->id, "tipo_notificacion_id" => $tipo_notificacion_id]);
            //Generar citatorio de audiencia
            if($parte->tipo_parte_id == 2){
                event(new GenerateDocumentResolution($audiencia->id, $expediente->solicitud_id, 14, 4,null,$parte->id));
            }
        }
        return $audiencia;
    }
    /**
     * Funcion para obtener la vista del calendario central
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function calendarizarCentral(Request $request) {
        if ($request->tipoAsignacion == 1) {
            $multiple = false;
        } else {
            $multiple = true;
        }
        DB::beginTransaction();
        try{
            $solicitud = Solicitud::find($request->solicitud_id);
            $ContadorController = new ContadorController();
            //Obtenemos el contador
            $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
            $edo_folio = $solicitud->centro->abreviatura;
            $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
            //Creamos el expediente de la solicitud
            $expediente = Expediente::create(["solicitud_id" => $request->solicitud_id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
            foreach ($solicitud->partes as $key => $parte) {
                if(count($parte->documentos) == 0){
                    $parte->ratifico = true;
                    $parte->update();
                }
            }
//                obtenemos el domicilio del centro
            $domicilio_centro = auth()->user()->centro->domicilio;
//                obtenemos el domicilio del citado
            $partes = $solicitud->partes;
            $domicilio_citado = null;
            foreach($partes as $parte){
                if($parte->tipo_parte_id == 2){
                    $domicilio_citado = $parte->domicilios()->first();
                    break;
                }
            }
            
            $solicitud->update(["estatus_solicitud_id" => 2, "ratificada" => true, "fecha_ratificacion" => now(),"inmediata" => false]);
            $fecha_notificacion = null;
            if((int)$request->tipo_notificacion_id == 2){
                $fecha_notificacion = self::obtenerFechaLimiteNotificacion($domicilio_centro,$domicilio_citado,$request->fecha_audiencia);
            }
            //Obtenemos el contador
            $folioAudiencia = $ContadorController->getContador(3, auth()->user()->centro_id);
            //creamos el registro de la audiencia
            if($request->fecha_cita == "" || $request->fecha_cita == null){
                $fecha_cita = null;
            }else{
                $fechaC = explode("/", $request->fecha_cita);
                $fecha_cita = $fechaC["2"]."-".$fechaC["1"]."-".$fechaC["0"];
            }
            $audiencia = Audiencia::create([
                "expediente_id" => $expediente->id,
                "multiple" => $multiple,
                "fecha_audiencia" => $request->fecha_audiencia,
                "fecha_limite_audiencia" => $fecha_notificacion,
                "hora_inicio" => $request->hora_inicio, 
                "hora_fin" => $request->hora_fin,
                "conciliador_id" =>  1,
                "numero_audiencia" => 1,
                "reprogramada" => false,
                "anio" => $folioAudiencia->anio,
                "folio" => $folioAudiencia->contador,
                "encontro_audiencia" => true,
                "fecha_cita" => $fecha_cita
            ]);
            $id_conciliador = null;
            foreach ($request->asignacion as $value) {
                if ($value["resolucion"]) {
                    $id_conciliador = $value["conciliador"];
                }
                ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $value["conciliador"], "solicitante" => $value["resolucion"]]);
                SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $value["sala"], "solicitante" => $value["resolucion"]]);
            }
            $audiencia->update(["conciliador_id" => $id_conciliador]);
            // Guardamos todas las Partes en la audiencia
            $partes = $solicitud->partes;
            $tipo_notificacion_id = null;
            foreach ($partes as $parte) {
                if($parte->tipo_parte_id != 1){
                    $tipo_notificacion_id = $request->tipo_notificacion_id;
                }
                AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parte->id]);
                if($parte->tipo_parte_id == 2){
                    event(new GenerateDocumentResolution($audiencia->id,$solicitud->id,14,4,null,$parte->id));
                }
            }
            if($tipo_notificacion_id != 1 && $tipo_notificacion_id != null){
                event(new RatificacionRealizada($audiencia->id,"citatorio"));
            }
            
            $audiencia = Audiencia::find($audiencia->id);
            $salas = [];
            foreach($audiencia->salasAudiencias as $sala){
                $sala->sala;
            }
            foreach($audiencia->conciliadoresAudiencias as $conciliador){
                $conciliador->conciliador->persona;
            }
            $acuse = Documento::where('documentable_type','App\Solicitud')->where('documentable_id',$solicitud->id)->where('clasificacion_archivo_id',40)->first();
            if($acuse != null){
                $acuse->delete();
            }
            event(new GenerateDocumentResolution("",$solicitud->id,40,6));
            DB::commit();
            return $audiencia;
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al confirmar la solicitud', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al confirmar la solicitud');
        }
    }

    /**
     * Funcion para obtener los momentos ocupados
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCalendario(Request $request) {
        // inicio obtenemos los datos del centro donde no trabajará
        $centro = auth()->user()->centro;
        $centroDisponibilidad = $centro->disponibilidades;
        $laboresCentro = array();
        foreach ($centroDisponibilidad as $key => $value) {
            array_push($laboresCentro, array("dow" => array($value["dia"]), "startTime" => $value["hora_inicio"], "endTime" => $value["hora_fin"]));
        }
        //fin obtenemos disponibilidad
        //inicio obtenemos incidencias del centro
        $incidenciasCentro = array();
        $centroIncidencias = $centro->incidencias;
        $arrayFechas = [];
        foreach ($centroIncidencias as $key => $value) {
            $arrayFechas = $this->validarIncidenciasCentro($value, $arrayFechas);
        }
        $arrayAudiencias = $this->getTodasAudienciasIndividuales($centro->id);
        $ev = array_merge($arrayFechas, $arrayAudiencias);
        //construimos el arreglo general
        $arregloGeneral = array();
        $arregloGeneral["laboresCentro"] = $laboresCentro;
        $arregloGeneral["incidenciasCentro"] = $ev;
        $arregloGeneral["duracionPromedio"] = $centro->duracionAudiencia;
        //obtenemos el minmaxtime
        $minmax = $this->getMinMaxTime($centro);
        $arregloGeneral["minTime"] = $minmax["hora_inicio"];
        $arregloGeneral["maxtime"] = $minmax["hora_fin"];
        return $arregloGeneral;
    }
    /**
     * Funcion para obtener los momentos ocupados
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCalendarioCentral(Request $request) {
        // inicio obtenemos los datos del centro donde no trabajará
        $centro = Centro::where("abreviatura","OCCFCRL")->first();
        $centroDisponibilidad = $centro->disponibilidades;
        $laboresCentro = array();
        foreach ($centroDisponibilidad as $key => $value) {
            array_push($laboresCentro, array("dow" => array($value["dia"]), "startTime" => $value["hora_inicio"], "endTime" => $value["hora_fin"]));
        }
        //fin obtenemos disponibilidad
        //inicio obtenemos incidencias del centro
        $incidenciasCentro = array();
        $centroIncidencias = $centro->incidencias;
        $arrayFechas = [];
        foreach ($centroIncidencias as $key => $value) {
            $arrayFechas = $this->validarIncidenciasCentro($value, $arrayFechas);
        }
        $arrayAudiencias = $this->getTodasAudienciasIndividuales($centro->id);
        $ev = array_merge($arrayFechas, $arrayAudiencias);
        //construimos el arreglo general
        $arregloGeneral = array();
        $arregloGeneral["laboresCentro"] = $laboresCentro;
        $arregloGeneral["incidenciasCentro"] = $ev;
        $arregloGeneral["duracionPromedio"] = $centro->duracionAudiencia;
        //obtenemos el minmaxtime
        $minmax = $this->getMinMaxTime($centro);
        $arregloGeneral["minTime"] = $minmax["hora_inicio"];
        $arregloGeneral["maxtime"] = $minmax["hora_fin"];
        return $arregloGeneral;
    }
    /**
     * Funcion para obtener los momentos ocupados
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCalendarioColectivas(Request $request) {
        // inicio obtenemos los datos del centro donde no trabajará
        $centro = auth()->user()->centro;
        $centroDisponibilidad = $centro->disponibilidades;
        $laboresCentro = array();
        foreach ($centroDisponibilidad as $key => $value) {
            array_push($laboresCentro, array("dow" => array($value["dia"]), "startTime" => $value["hora_inicio"], "endTime" => $value["hora_fin"]));
        }
        //fin obtenemos disponibilidad
        //inicio obtenemos incidencias del centro
        $incidenciasCentro = array();
        $centroIncidencias = $centro->incidencias;
        $arrayFechas = [];
        foreach ($centroIncidencias as $key => $value) {
            $arrayFechas = $this->validarIncidenciasCentro($value, $arrayFechas);
        }
        $arrayAudiencias = $this->getTodasAudienciasColectivas($centro->id);
        $ev = array_merge($arrayFechas, $arrayAudiencias);
        //construimos el arreglo general
        $arregloGeneral = array();
        $arregloGeneral["laboresCentro"] = $laboresCentro;
        $arregloGeneral["incidenciasCentro"] = $ev;
        $arregloGeneral["duracionPromedio"] = $centro->duracionAudiencia;
        //obtenemos el minmaxtime
        $minmax = $this->getMinMaxTime($centro);
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
    public function validarIncidenciasCentro($incidencia, $arrayFechas) {
        $dates = array();
        $current = strtotime($incidencia["fecha_inicio"]);
        $last = strtotime($incidencia["fecha_fin"]);
        $step = '+1 day';
        $output_format = 'Y-m-d';
        while ($current <= $last) {
            $arrayFechas[] = array("start" => date($output_format, $current), "end" => date($output_format, $current), "rendering" => 'background', "backgroundColor" => 'red', "allDay" => true);
            $arrayFechas[] = array("start" => date($output_format, $current) . " 00:00:00", "end" => date($output_format, $current) . " 23:59:00", "rendering" => 'background', "backgroundColor" => 'red', "allDay" => false);
            $current = strtotime($step, $current);
        }
        return $arrayFechas;
    }

    /**
     * Funcion para obtener las audiencias de el centro
     * @param id $centro_id
     * @return array
     */
    public function getTodasAudiencias() {
        $solicitudes = Solicitud::where("centro_id", auth()->user()->centro_id)->where("ratificada",true)->get();
        $audiencias = [];
        foreach ($solicitudes as $solicitud) {
            $audienciasSolicitud = $solicitud->expediente->audiencia;
            foreach ($audienciasSolicitud as $audiencia) {
                if (new Carbon($audiencia->fecha_audiencia) >= now()) {
                    array_push($audiencias, $audiencia);
                }
            }
        }
        $arrayEventos = [];
        foreach ($audiencias as $audiencia) {
            $start = $audiencia->fecha_audiencia . " " . $audiencia->hora_inicio;
            $end = $audiencia->fecha_audiencia . " " . $audiencia->hora_fin;
            array_push($arrayEventos, array("start" => $start, "end" => $end, "title" => $audiencia->folio . "/" . $audiencia->anio, "color" => "#00ACAC", "audiencia_id" => $audiencia->id));
        }
        return $arrayEventos;
    }
    /**
     * Funcion para obtener las audiencias individuales de el centro
     * @param id $centro_id
     * @return array
     */
    public function getTodasAudienciasIndividuales() {
        $solicitudes = Solicitud::where("centro_id", auth()->user()->centro_id)->where("ratificada",true)->whereIn("tipo_solicitud_id",[1,2])->get();
        $audiencias = [];
        foreach ($solicitudes as $solicitud) {
            $audienciasSolicitud = $solicitud->expediente->audiencia;
            foreach ($audienciasSolicitud as $audiencia) {
//                if (new Carbon($audiencia->fecha_audiencia) >= date("Y-m-d")) {
                    array_push($audiencias, $audiencia);
//                }
            }
        }
        $arrayEventos = [];
        foreach ($audiencias as $audiencia) {
            $start = $audiencia->fecha_audiencia . " " . $audiencia->hora_inicio;
            $end = $audiencia->fecha_audiencia . " " . $audiencia->hora_fin;
            array_push($arrayEventos, array("start" => $start, "end" => $end, "title" => $audiencia->folio . "/" . $audiencia->anio, "color" => "#00ACAC", "audiencia_id" => $audiencia->id));
        }
        return $arrayEventos;
    }
    /**
     * Funcion para obtener las audiencias colectivas
     * @param id $centro_id
     * @return array
     */
    public function getTodasAudienciasColectivas() {
        $solicitudes = Solicitud::whereIn("tipo_solicitud_id", [3,4])->where("ratificada",true)->get();
        $audiencias = [];
        foreach ($solicitudes as $solicitud) {
            $audienciasSolicitud = $solicitud->expediente->audiencia;
            foreach ($audienciasSolicitud as $audiencia) {
//                if (new Carbon($audiencia->fecha_audiencia) >= now()) {
                    array_push($audiencias, $audiencia);
//                }
            }
        }
        $arrayEventos = [];
        foreach ($audiencias as $audiencia) {
            $start = $audiencia->fecha_audiencia . " " . $audiencia->hora_inicio;
            $end = $audiencia->fecha_audiencia . " " . $audiencia->hora_fin;
            array_push($arrayEventos, array("start" => $start, "end" => $end, "title" => $audiencia->folio . "/" . $audiencia->anio, "color" => "#00ACAC", "audiencia_id" => $audiencia->id));
        }
        return $arrayEventos;
    }
    public function getAudienciasSinFecha(){
        $solicitudes = Solicitud::where("centro_id", auth()->user()->centro_id)->where("ratificada",true)->get();
        $audiencias = [];
        foreach ($solicitudes as $solicitud) {
            $audienciasSolicitud = $solicitud->expediente->audiencia;
            foreach ($audienciasSolicitud as $audiencia) {
                if (!$audiencia->encontro_audiencia) {
                    array_push($audiencias, $audiencia);
                }
            }
        }
        return $audiencias;
    }

    /**
     * Funcion para guardar la resolución de la audiencia
     * @param id $centro_id
     * @return array
     */
    function Resolucion(Request $request) {
        try {
            DB::beginTransaction();
            $audiencia = Audiencia::find($request->audiencia_id);
            if (!$audiencia->finalizada) {

                if ($request->timeline) {
                    $audiencia->update(array("resolucion_id" => $request->resolucion_id, "finalizada" => true));
                } else {
                    $audiencia->update(array("convenio" => $request->convenio, "desahogo" => $request->desahogo, "resolucion_id" => $request->resolucion_id, "finalizada" => true));
                    foreach ($request->comparecientes as $compareciente) {
                        Compareciente::create(["parte_id" => $compareciente, "audiencia_id" => $audiencia->id, "presentado" => true]);
                    }
                }
                if($audiencia->resolucion_id != 2){
                    $solicitud = $audiencia->expediente->solicitud;
                    $solicitud->update([
                        "estatus_solicitud_id" => 3
                    ]);
                }
                $evidencia = ($request->evidencia) ? $request->evidencia: "";
                $this->guardarRelaciones($audiencia, $request->listaRelacion, $request->listaConceptos, $request->listaFechasPago );
                $etapaAudiencia = EtapaResolucionAudiencia::create([
                    "etapa_resolucion_id" => 6,
                    "audiencia_id" => $audiencia->id,
                    "evidencia" => $evidencia
                ]);
                    
            }
                
            DB::commit();
            return $audiencia;
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al registrar la resolucion', 'Error');
        }
    }

    /**
     * Funcion para guardar las resoluciones individuales de las audiencias
     * @param Audiencia $audiencia
     * @param type $arrayRelaciones
     */
    public function guardarRelaciones(Audiencia $audiencia, $arrayRelaciones = array(), $listaConceptos = array(), $listaFechasPago = array()) {
        $partes = $audiencia->audienciaParte;
        $solicitantes = $this->getSolicitantes($audiencia);
        $solicitados = $this->getSolicitados($audiencia);
        $arrayMultado = [];
        $huboConvenio = false;
        foreach ($solicitantes as $solicitante) {
            foreach ($solicitados as $solicitado) {
                $bandera = true;
                if ($arrayRelaciones != null) {
                    foreach ($arrayRelaciones as $relacion) {
                        //
                        $parte_solicitante =Parte::find($relacion["parte_solicitante_id"]);
                        if($parte_solicitante->tipo_parte_id == 3){
                            $parte_solicitante =Parte::find($parte_solicitante->parte_representada_id);
                        }
                        //
                        $parte_solicitado =Parte::find($relacion["parte_solicitado_id"]);
                        if($parte_solicitado->tipo_parte_id == 3){
                            $parte_solicitado =Parte::find($parte_solicitado->parte_representada_id);
                        }
                        
                        if ($solicitante->parte_id == $parte_solicitante->id && $solicitado->parte_id == $parte_solicitado->id) {
                            $terminacion = 3;
                            $huboConvenio = true;
                            // se genera convenio
                            // event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,16,2,$solicitante->parte_id,$solicitado->parte_id));
                            $bandera = false;
                        } else {

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
                if ($bandera) {
                      //Se consulta comparecencia de solicitante
                      $parteS = $solicitante->parte;
                      if ($parteS->tipo_persona_id == 2) {
                          $compareciente_parte = Parte::where("parte_representada_id", $parteS->id)->first();
                          if ($compareciente_parte != null) {
                              $comparecienteSol = Compareciente::where('parte_id', $compareciente_parte->id)->first();
                          } else {
                              $comparecienteSol = null;
                          }
                      } else {
                          $comparecienteSol = Compareciente::where('parte_id', $solicitante->parte_id)->first();
                      }
                      //Se consulta comparecencia de citado
                      $parte = $solicitado->parte;
                      $comparecienteCit = null;
                      if($parte->tipo_persona_id == 1){
                        $comparecienteCit = Compareciente::where('parte_id', $solicitado->parte_id)->first();
                      }
                      if(!$comparecienteCit){
                        if ($parte->tipo_persona_id == 2) {
                            $compareciente_parte = Parte::where("parte_representada_id", $parte->id)->first();
                            if ($compareciente_parte != null) {
                                $comparecienteCit = Compareciente::where('parte_id', $compareciente_parte->id)->first();
                            } else {
                                $comparecienteCit = null;
                            }
                        }
                    }

                    $terminacion = 1;
                    if ($audiencia->resolucion_id == 3) {
                        //no hubo convenio, guarda resolucion para todas las partes
                        $terminacion = 5;
                        //se genera el acta de no conciliacion para todos los casos
                        event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 17, 1, $solicitante->parte_id, $solicitado->parte_id));

                        $parte = $solicitado->parte;
                        if ($parte->tipo_persona_id == 2) {
                            $compareciente_parte = Parte::where("parte_representada_id", $parte->id)->first();
                            if ($compareciente_parte != null) {
                                $compareciente = Compareciente::where('parte_id', $compareciente_parte->id)->first();
                            } else {
                                $compareciente = null;
                            }
                        } else {
                            $compareciente = Compareciente::where('parte_id', $solicitado->parte_id)->first();
                        }
                        // Si no es compareciente se genera multa
                        if ($compareciente == null) {
                            $multable = false;
                            $audienciaParte = AudienciaParte::where('audiencia_id',$audiencia->id)->where('parte_id',$solicitado->parte_id)->first();
                            if( $audienciaParte && $audienciaParte->finalizado ==  "FINALIZADO EXITOSAMENTE"){
                                if(array_search($solicitado->parte_id,$arrayMultado) === false){
                                    // Se genera archivo de acta de multa
                                    event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 18, 7, null, $solicitado->parte_id));
                                    array_push($arrayMultado,$solicitado->parte_id);
                                }
                            }
                            // Se genera multa

                        }
                    } else if ($audiencia->resolucion_id == 1) {
                        if($comparecienteSol != null && $comparecienteCit != null){
                            $terminacion = 3;
                            $huboConvenio = true;
                        }else if($comparecienteSol != null){
                            $terminacion = 4;
                        }else{
                            $terminacion = 1;
                        }
                    } else if ($audiencia->resolucion_id == 2) {
                        //no hubo convenio pero se agenda nueva audiencia, guarda para todos las partes
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
                if ( isset($resolucionParte)) { //Hubo conciliacion
                    // if($audiencia->resolucion_id == 1 ){ //Hubo conciliacion
                    // if (isset($listaConceptos)) {
                    //     if (count($listaConceptos) > 0) {
                    //         foreach ($listaConceptos as $key => $conceptosSolicitante) {//solicitantes
                    //             // foreach($conceptosSolicitante as $ke=>$conceptosPago){//conceptos por solicitante
                    //             if ($key == $solicitante->parte_id) {
                    //                 foreach ($conceptosSolicitante as $k => $concepto) {
                    //                     ResolucionParteConcepto::create([
                    //                         "resolucion_partes_id" => $resolucionParte->id,
                    //                         "parte_id" => $solicitante->parte_id,
                    //                         "concepto_pago_resoluciones_id" => $concepto["concepto_pago_resoluciones_id"],
                    //                         "dias" => intval($concepto["dias"]),
                    //                         "monto" => $concepto["monto"],
                    //                         "otro" => $concepto["otro"]
                    //                     ]);
                    //                 }
                    //             }
                    //             // }
                    //         }
                    //     }
                    // }
                    if ($terminacion == 3) {
                        $huboConvenio = true;
                        //Se consulta comparecencia de citado
                        $parte = $solicitado->parte;
                        if ($parte->tipo_persona_id == 2) {
                            $compareciente_parte = Parte::where("parte_representada_id", $parte->id)->first();
                            if ($compareciente_parte != null) {
                                $comparecienteCit = Compareciente::where('parte_id', $compareciente_parte->id)->first();
                                // dd($comparecienteCit);
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
            $solicitanteComparecio = $solicitante->parte->compareciente->where('audiencia_id',$audiencia->id)->first();
            if($solicitanteComparecio != null){
                if (isset($listaConceptos)) {
                    if (count($listaConceptos) > 0) {
                        foreach ($listaConceptos as $key => $conceptosSolicitante) {//solicitantes
                            if ($key == $solicitante->parte_id) {
                                foreach ($conceptosSolicitante as $k => $concepto) {
                                    ResolucionParteConcepto::create([
                                        "resolucion_partes_id" => null,//$resolucionParte->id,
                                        "audiencia_parte_id" => $solicitante->id,
                                        "concepto_pago_resoluciones_id" => $concepto["concepto_pago_resoluciones_id"],
                                        "dias" => intval($concepto["dias"]),
                                        "monto" => $concepto["monto"],
                                        "otro" => $concepto["otro"]
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

        }
        // Termina consulta de comparecencia de solicitante
        if($huboConvenio){
            if (isset($listaFechasPago)) { //se registran pagos diferidos
                if (count($listaFechasPago) > 0) {
                    foreach ($listaFechasPago as $key => $fechaPago) {
                        ResolucionPagoDiferido::create([
                            "audiencia_id" => $audiencia->id,
                            "monto" => $fechaPago["monto_pago"],
                            "fecha_pago" => Carbon::createFromFormat('d/m/Y',$fechaPago["fecha_pago"])->format('Y-m-d')
                        ]);
                    }
                }
            }
            foreach ($solicitantes as $solicitante) {
                $convenio = ResolucionPartes::where('parte_solicitante_id',$solicitante->parte_id)->where('terminacion_bilateral_id',3)->first();
                if($convenio != null){
                    //generar convenio
                    event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 16, 2,$solicitante->parte_id));
                }else{
                    $noConciliacion = ResolucionPartes::where('parte_solicitante_id',$solicitante->parte_id)->where('terminacion_bilateral_id',5)->first();
                    if($noConciliacion != null){
                        foreach ($solicitados as $solicitado) {
                            event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 17, 1, $solicitante->parte_id, $solicitado->parte_id));
                        }
                    }
                }
            }
        }
        //generar acta de audiencia
        event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 15, 3));
    }
    /**
     * Funcion para generar constancia de no comparecencia en fecha de pago
     * @param type $audiencia_id, Pagodiferido_id
     * @return type
     */
    function generarConstanciaNoPago(Request $request) {
        $pagoDiferido = ResolucionPagoDiferido::find($request->idPagoDiferido);
        $pagoDiferido->update([
            "pagado" => false
        ]);
        //Se genera el acta de no comparecencia en fecha de pago
        event(new GenerateDocumentResolution($request->audiencia_id, $request->solicitud_id, 19, 11));//11
        return $pagoDiferido;
    }
    /**
     * Funcion para generar constancia de no comparecencia en fecha de pago
     * @param type $audiencia_id, Pagodiferido_id
     * @return type
     */
    function registrarPagoDiferido(Request $request) {
        try {
            $pagoDiferido = ResolucionPagoDiferido::find($request->idPagoDiferido);
            $pagoDiferido->update([
                "pagado" => true
            ]);

            $pagos = ResolucionPagoDiferido::where('audiencia_id',$request->audiencia_id)->orderBy('fecha_pago')->get();
            $ultimoPago = $pagos->last()->id;
            $pagados = true;
            foreach($pagos as $pago) {
                if($pago->pagado == false){
                    $pagados = false;
                }
            }
            //si los pagos anteriores han sido pagados y es el ultimo pago
            //generar constancia de cumplimiento de convenio
            //if($pagados && ($ultimoPago == $request->idPagoDiferido)){
            if($pagados){
                event(new GenerateDocumentResolution($request->audiencia_id, $request->solicitud_id, 45, 12));
            }
            return $pagoDiferido;
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
            " Se emitió el siguiente mensaje: ". $e->getMessage().
            " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
        }
        
    }

    /**
     * Funcion para obtener los documentos de la audiencia
     * @param type $audiencia_id
     * @return type
     */
    function getDocumentosAudiencia($audiencia_id) {
        $audiencia = Audiencia::find($audiencia_id);
        $documentos = $audiencia->documentos;
        foreach ($documentos as $documento) {
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
    public function GetPartesFisicas($audiencia_id) {
        $audiencia = Audiencia::find($audiencia_id);
//        dd($audiencia->expediente->solicitud->partes);
        $partes = [];
        foreach ($audiencia->audienciaParte as $audienciaParte) {
            if ($audienciaParte->parte->tipo_persona_id == 1) {
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
    public function validarPartes($audiencia_id) {
        // Obtenemos las partes de la audiencia que sean de tipo persona moral;
        $audiencia = Audiencia::find($audiencia_id);
        $partes = $audiencia->expediente->solicitud->partes->where("tipo_persona_id", "2");
        // Validamos si hay personas morales
        if (count($partes)) {
            foreach ($partes as $parte) {
                $representante = Parte::where("parte_representada_id", $parte->id)->get();
                if (!count($representante)) {
                    // Si la persona moral no tiene representante no se puede guardar
                    return ["pasa" => false];
                }
            }
            // Si siempre tiene representante
            return ["pasa" => true];
        } else {
            // Si no hay personas Morales se puede guardar la resolución
            return ["pasa" => true];
        }
    }

    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitante
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitante
     */
    public function getSolicitantes(Audiencia $audiencia) {
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
    public function getSolicitados(Audiencia $audiencia) {
        $solicitados = [];
        foreach ($audiencia->audienciaParte as $parte) {
            if ($parte->parte->tipo_parte_id == 2) {
                $solicitados[] = $parte;
            }
        }
        return $solicitados;
    }

    public function NuevaAudiencia(Request $request) {
        DB::beginTransaction();
        ##Obtenemos la audiencia origen
        $audiencia = Audiencia::find($request->audiencia_id);

        ## Validamos si la audiencia se calendariza o solo es para guardar una resolución distinta
        if ($request->nuevaCalendarizacion == "S") {
            $fecha_audiencia = $request->fecha_audiencia;
            $hora_inicio = $request->hora_inicio;
            $hora_fin = $request->hora_fin;
            if ($request->tipoAsignacion == 1) {
                $multiple = false;
            } else {
                $multiple = true;
            }
        } else {
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
            "conciliador_id" => $audiencia->conciliador_id,
            "numero_audiencia" => 1,
            "reprogramada" => true,
            "anio" => $folio->anio,
            "folio" => $folio->contador
        ]);
        ## si la audiencia se calendariza se deben guardar los datos recibidos en el arreglo, si no se copian los de la audiencia origen
        if ($request->nuevaCalendarizacion == "S") {
//            $id_conciliador = null;
            foreach ($request->asignacion as $value) {
                SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $value["sala"], "solicitante" => $value["resolucion"]]);
            }
//            $audienciaN->update(["conciliador_id" => $id_conciliador]);
        } else {
            foreach ($audiencia->salasAudiencias as $sala) {
                SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $sala->sala_id, "solicitante" => $sala->solicitante]);
            }
        }
        foreach ($audiencia->conciliadoresAudiencias as $conciliador) {
            ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $conciliador->conciliador_id, "solicitante" => $conciliador->solicitante]);
        }

        ##Finalmente guardamos los datos de las partes recibidas
        $arregloPartesAgregadas = array();
        foreach ($request->listaRelaciones as $relacion) {
            ##Validamos que el solicitante no exista
            $pasaSolicitante = true;
            foreach ($arregloPartesAgregadas as $arreglo) {
                if ($relacion["parte_solicitante_id"] == $arreglo) {
                    $pasaSolicitante = false;
                }
            }
            $tipo_notificacion_id = 1;
            if ($pasaSolicitante) {
                $arregloPartesAgregadas[] = $relacion["parte_solicitante_id"];
                AudienciaParte::create(["audiencia_id" => $audienciaN->id, "parte_id" => $relacion["parte_solicitante_id"], "tipo_notificacion_id" => $tipo_notificacion_id]);
                // buscamos representantes legales de esta parte
                $parte = $audiencia->expediente->solicitud->partes()->where("parte_representada_id",$relacion["parte_solicitante_id"])->first();
                if($parte != null){
                    AudienciaParte::create(["audiencia_id" => $audienciaN->id, "parte_id" => $parte->id, "tipo_notificacion_id" => $tipo_notificacion_id]);
                }
            }
            ##Validamos que el solicitado no exista
            $pasaSolicitado = true;
            foreach ($arregloPartesAgregadas as $arreglo) {
                if ($relacion["parte_solicitada_id"] == $arreglo) {
                    $pasaSolicitado = false;
                }
            }
            if ($pasaSolicitado) {
                $arregloPartesAgregadas[] = $relacion["parte_solicitada_id"];
                AudienciaParte::create(["audiencia_id" => $audienciaN->id, "parte_id" => $relacion["parte_solicitada_id"]]);
                //generar citatorio de audiencia
                event(new GenerateDocumentResolution($audienciaN->id,$audienciaN->expediente->solicitud->id,14,4,null,$relacion["parte_solicitada_id"]));
                // buscamos representantes legales de esta parte
                $parte = $audiencia->expediente->solicitud->partes()->where("parte_representada_id",$relacion["parte_solicitada_id"])->first();
                if($parte != null){
                    AudienciaParte::create(["audiencia_id" => $audienciaN->id, "parte_id" => $parte->id, "tipo_notificacion_id" => $tipo_notificacion_id]);
                }
            }
            $resolucion = ResolucionPartes::find($relacion["id"]);
            $resolucion->update(["nuevaAudiencia" => true]);
        }
        //$expediente = Expediente::find($audiencia->expediente_id);
        //event(new GenerateDocumentResolution($audiencia->id, $expediente->solicitud_id, 14, 4));
        DB::commit();
        return $audienciaN;
        
    }

    ############################### A partir de este punto comienzan las funciones para el chacklist ########################################

    public function AgendaConciliador() {
        return view('expediente.audiencias.agendaConciliador');
    }

    public function GetAudienciaConciliador() {
//        obtenemos los datos del conciliador
        $conciliador = auth()->user()->persona->conciliador;
//        obtenemos las audiencias programadas a partir de el día de hoy
        $arrayEventos = [];
        if ($conciliador != null) {
            $audiencias = $conciliador->ConciliadorAudiencia;
            foreach ($audiencias as $audiencia) {
                $start = $audiencia->audiencia->fecha_audiencia . " " . $audiencia->audiencia->hora_inicio;
                $end = $audiencia->audiencia->fecha_audiencia . " " . $audiencia->audiencia->hora_fin;
                array_push($arrayEventos, array("start" => $start, "end" => $end, "title" => $audiencia->audiencia->folio . "/" . $audiencia->audiencia->anio, "color" => "#00ACAC", "audiencia_id" => $audiencia->audiencia->id));
            }
        }
        // obtenemos el horario menor y mayor del conciliador
        $maxMinDisponibilidad = $this->getMinMaxConciliador($conciliador);
        $response = array("eventos" => $arrayEventos, "minTime" => $maxMinDisponibilidad["hora_inicio"], "maxTime" => $maxMinDisponibilidad["hora_fin"]);
        return $response;
    }

    public function guiaAudiencia($id) {
        $etapa_resolucion = EtapaResolucion::orderBy('paso')->get();
        $audiencia = Audiencia::find($id);
        $partes = array();
        $solicitantesComparecientes = array();
        foreach ($audiencia->audienciaParte as $key => $parte) {
            $parte->parte->tipoParte = $parte->parte->tipoParte;
            $partes[$key] = $parte->parte;
        }
        $solicitud_id = $audiencia->expediente->solicitud->id;
        $audiencia->partes = $partes;
        $periodicidades = $this->cacheModel('periodicidades', Periodicidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones', Ocupacion::class);
        $jornadas = $this->cacheModel('jornadas', Jornada::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales', GiroComercial::class);
        $resoluciones = $this->cacheModel('resoluciones', Resolucion::class);
        $terminacion_bilaterales = $this->cacheModel('terminacion_bilaterales', TerminacionBilateral::class);
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);

        foreach ($audiencia->comparecientes as $key => $compareciente) {
            $compareciente->parte = $compareciente->parte;
            $parteRep = [];
            //representante legal
            if ($compareciente->parte->tipo_parte_id == 3 && $compareciente->parte->parte_representada_id != null) {
                $parteRep = Parte::find($compareciente->parte->parte_representada_id);
            }
            $compareciente->parte->parteRepresentada = $parteRep;
            $comparecientes[$key] = $compareciente;
            if ($compareciente->parte->tipo_parte_id == 1){
                $solicitantesComparecientes[$key] = $compareciente;
            }
        }
        $audiencia->solicitantesComparecientes = $solicitantesComparecientes;
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        $conciliador = Conciliador::find($audiencia->conciliador_id);
        $motivos_archivo = MotivoArchivado::all();
        $concepto_pago_resoluciones = ConceptoPagoResolucion::where('id', '<=', 9)->get();
        $concepto_pago_reinstalacion = ConceptoPagoResolucion::whereIn('id', [8, 9, 10])->get();
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->get();
        $estados = Estado::all();
        $tipos_vialidades = $this->cacheModel('tipos_vialidades', TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos', TipoAsentamiento::class);
        $lengua_indigena = $this->cacheModel('lengua_indigena',LenguaIndigena::class);
        $generos = $this->cacheModel('generos',Genero::class);
        $tipo_contacto = $this->cacheModel('tipo_contacto',TipoContacto::class);
        $nacionalidades = $this->cacheModel('nacionalidades',Nacionalidad::class);
        return view('expediente.audiencias.etapa_resolucion', compact('etapa_resolucion', 'audiencia', 'periodicidades', 'ocupaciones', 'jornadas', 'giros_comerciales', 'resoluciones', 'concepto_pago_resoluciones', 'concepto_pago_reinstalacion', 'motivos_archivo', 'clasificacion_archivos_Representante', 'clasificacion_archivo', 'terminacion_bilaterales', 'solicitud_id','conciliador','estados','tipos_vialidades','tipos_asentamientos','lengua_indigena','generos','tipo_contacto','nacionalidades'));
    }

    public function resolucionColectiva($id) {
        $audiencia = Audiencia::find($id);
        foreach ($audiencia->audienciaParte as $key => $parte) {
            $parte->parte->tipoParte = $parte->parte->tipoParte;
            $partes[$key] = $parte->parte;
        }
        $audiencia->partes = $partes;
        $solicitud = $audiencia->expediente->solicitud;
        $audiencia->solicitantes = $this->getSolicitantes($audiencia);
        $audiencia->solicitados = $this->getSolicitados($audiencia);
        // dd($solicitud->folio);
        $plantilla['plantilla_header'] = view('documentos._header_documentos_colectivo_default',compact('solicitud'))->render();
        $plantilla['plantilla_body'] = "";
        $plantilla['plantilla_footer'] = "";
        $resoluciones = $this->cacheModel('resoluciones', Resolucion::class);
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->get();
        $motivos_archivo = MotivoArchivado::all();
        $centro = Centro::where('central',true)->first();
        return view('expediente.audiencias.resolucion_colectiva', compact('plantilla','solicitud','audiencia','resoluciones','clasificacion_archivo','clasificacion_archivos_Representante','motivos_archivo','centro'));
    }

    public function guardarAudienciaColectiva(Request $request) {
        //Creamos el registro
        DB::beginTransaction();
        try{
            $idAudiencia = $request->audiencia_id;

            $audiencia = Audiencia::find($idAudiencia);
            $solicitud = $audiencia->expediente->solicitud;
            if($request->resolucion_id != ""){
                if($request->fileActaAudiencia){
                    $archivo = $request->fileActaAudiencia;
                    $clasificacion_archivo= 15;
                    $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $idAudiencia;
                    Storage::makeDirectory($directorio);
                    $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                    
                    $path = $archivo->store($directorio);
                    $uuid = Str::uuid();
                    $documento = $audiencia->documentos()->create([
                        "nombre" => str_replace($directorio."/", '',$path),
                        "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        "descripcion" => $tipoArchivo->nombre,
                        "ruta" => $path,
                        "tipo_almacen" => "local",
                        "uri" => $path,
                        "uuid" => $uuid,
                        "longitud" => round(Storage::size($path) / 1024, 2),
                        "firmado" => "false",
                        "clasificacion_archivo_id" => $tipoArchivo->id ,
                    ]);
                }else{
                    $html = $request['audiencia_body'];
                    $htmlHeader = view('documentos._header_documentos_colectivo_default',compact('solicitud'))->render();
                    $archivo = $this->guardarDocumento($idAudiencia,$html,$htmlHeader,15);
                }

                $audiencia->update(array("resolucion_id" => $request->resolucion_id, "finalizada" => true));
                if($request->resolucion_id != 2){
                    $solicitud = $audiencia->expediente->solicitud;
                    $solicitud->update([
                        "estatus_solicitud_id" => 3
                    ]);
                }
                $solicitantes = $this->getSolicitantes($audiencia);
                $solicitados = $this->getSolicitados($audiencia);
                $arrayRelaciones = $request->listaRelacion;
                foreach ($solicitantes as $solicitante) {
                    foreach ($solicitados as $solicitado) {
                        $bandera = true;
                        if ($arrayRelaciones != null) {
                            foreach ($arrayRelaciones as $relacion) {
                                if ($solicitante->parte_id == $relacion["parte_solicitante_id"] && $solicitado->parte_id == $relacion["parte_solicitado_id"]) {
                                    $terminacion = 3;
                                    $bandera = false;
                                } else {

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
                        if ($bandera) {
                            $terminacion = 1;
                            if ($audiencia->resolucion_id == 3) {
                                //se genera el acta de no conciliacion para todos los casos
                                event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 17, 1, $solicitante->parte_id, $solicitado->parte_id));
                                $terminacion = 5;
                            } else if ($audiencia->resolucion_id == 1) {
                                $terminacion = 3;
                            } else if ($audiencia->resolucion_id == 2) {
                                $terminacion = 2;
                            }
                            $resolucionParte = ResolucionPartes::create([
                                        "audiencia_id" => $audiencia->id,
                                        "parte_solicitante_id" => $solicitante->parte_id,
                                        "parte_solicitada_id" => $solicitado->parte_id,
                                        "terminacion_bilateral_id" => $terminacion
                            ]);
                        }
                    }
                }
                //guardar conceptos de pago para Convenio
                if ($audiencia->resolucion_id == 1 ) { //Hubo conciliacion
                    if($request->fileConvenio){
                        $archivo = $request->fileConvenio;
                        $clasificacion_archivo= 16;
                        $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $idAudiencia;
                        Storage::makeDirectory($directorio);
                        $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                        
                        $path = $archivo->store($directorio);
                        $uuid = Str::uuid();
                        $documento = $audiencia->documentos()->create([
                            "nombre" => str_replace($directorio."/", '',$path),
                            "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                            // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                            "descripcion" => $tipoArchivo->nombre,
                            "ruta" => $path,
                            "tipo_almacen" => "local",
                            "uri" => $path,
                            "uuid" => $uuid,
                            "longitud" => round(Storage::size($path) / 1024, 2),
                            "firmado" => "false",
                            "clasificacion_archivo_id" => $tipoArchivo->id ,
                        ]);
                    }else{
                        //Se genera el convenio
                        $html = $request['convenio_body'];
                        $htmlHeader = view('documentos._header_documentos_colectivo_default',compact('solicitud'))->render();
                        $archivo = $this->guardarDocumento($idAudiencia,$html,$htmlHeader,16);
                    }
                }
            }else{
                $html = $request['no_comparece_body'];
                $htmlHeader = view('documentos._header_documentos_colectivo_default',compact('solicitud'))->render();
                $archivo = $this->guardarDocumento($idAudiencia,$html,$htmlHeader,41);
                $audiencia->update(array("resolucion_id" => 3, "finalizada" => true));
                $solicitantes = $this->getSolicitantes($audiencia);
                $solicitados = $this->getSolicitados($audiencia);
                foreach ($solicitantes as $solicitante) {
                    foreach ($solicitados as $solicitado) {
                        $resolucionParte = ResolucionPartes::create([
                            "audiencia_id" => $audiencia->id,
                            "parte_solicitante_id" => $solicitante->parte_id,
                            "parte_solicitada_id" => $solicitado->parte_id,
                            "terminacion_bilateral_id" => 1
                        ]);
                    }
                }
            }
            DB::commit();
            return $audiencia;
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al registrar los comparecientes', 'Error');
        }
    }

    public function guardarDocumento($idAudiencia,$html,$htmlHeader,$tipo_archivo_id){
        $audiencia = Audiencia::find($idAudiencia);
        $tipoArchivo = ClasificacionArchivo::find($tipo_archivo_id);
        $uuid = Str::uuid();
        $archivo = $audiencia->documentos()->create(["descripcion" => "".$tipoArchivo->nombre ,'uuid'=> $uuid]);

        $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $idAudiencia;

        $nombreArchivo = $tipoArchivo->nombre;
        $nombreArchivo = $this->eliminar_acentos(str_replace(" ","",$nombreArchivo));
        $path = $directorio . "/".$nombreArchivo . $archivo->id . '.pdf';
        $fullPath = storage_path('app/' . $directorio) . "/".$nombreArchivo . $archivo->id . '.pdf';
        $this->renderPDFCustom($html, $htmlHeader, $fullPath);

        $archivo->update([
            "nombre" => str_replace($directorio . "/", '', $path),
            "nombre_original" => str_replace($directorio . "/", '', $path), //str_replace($directorio, '',$path->getClientOriginalName()),
            "descripcion" => "Documento de solicitud " . $tipoArchivo->nombre,
            "ruta" => $path,
            "tipo_almacen" => "local",
            "uri" => $path,
            "longitud" => round(Storage::size($path) / 1024, 2),
            "firmado" => "false",
            "clasificacion_archivo_id" => $tipoArchivo->id,
        ]);
        return $archivo;
    }

    public function guardarComparecientes() {
        DB::beginTransaction();
        try {
            $solicitantes = false;
            $citados = false;
            $citadosArray = array();
            $audiencia_id = $this->request->audiencia_id;
            $audiencia = Audiencia::find($audiencia_id);
            $response = array("tipo" => 6,"response" => null);
            $notificar = false;
            $citatorio = false;
            $audiencia_notificar_id = null;
            if (!$audiencia->finalizada) {
                $totalCitados = 0;
                foreach($audiencia->audienciaParte as $parte){
                    if($parte->parte->tipo_parte_id == 2){
                        $totalCitados++;
                    }
                }
                $totalCitadosComparecen = 0;
                if(isset($this->request->comparecientes)){
                    foreach ($this->request->comparecientes as $compareciente) {
                        $parte_compareciente = Parte::find($compareciente);
                        if ($parte_compareciente->tipo_parte_id == 1) {
                            $solicitantes = true;
                        } else if ($parte_compareciente->tipo_parte_id == 2) {
                            $citados = true;
                                $citadosArray[]=$parte_compareciente;
                                if($parte_compareciente->tipo_persona_id == 1){
                                    $totalCitadosComparecen++;
                                }
                        } else if ($parte_compareciente->tipo_parte_id == 3) {
                            $parte_representada = Parte::find($parte_compareciente->parte_representada_id);
                            if ($parte_representada->tipo_parte_id == 1) {
                                $solicitantes = true;
                            } else if ($parte_representada->tipo_parte_id == 2) {
                                $citados = true;
                                    $totalCitadosComparecen++;
                            }
                        }
                        Compareciente::create(["parte_id" => $compareciente, "audiencia_id" => $this->request->audiencia_id, "presentado" => true]);
                    }
                }
                if (!$solicitantes) {
                    // Aqui aplica el caso 1 donde no comparece el solicitante y se finaliza la solicitud por no comparecencia
                    //Archivado y se genera formato de acta de archivado por no comparecencia

                    $audiencia->update(array("resolucion_id" => 4, "finalizada" => true));
                    $solicitantesA = $this->getSolicitantes($audiencia);
                    $solicitados = $this->getSolicitados($audiencia);
                    foreach ($solicitantesA as $solicitante) {
                        foreach ($solicitados as $solicitado) {
                            $resolucionParte = ResolucionPartes::create([
                                "audiencia_id" => $audiencia->id,
                                "parte_solicitante_id" => $solicitante->parte_id,
                                "parte_solicitada_id" => $solicitado->parte_id,
                                "terminacion_bilateral_id" => 1
                            ]);
                            if($audiencia->expediente->solicitud->tipo_solicitud_id == 1){
                                event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 41, 8, $solicitante->parte_id, $solicitado->parte_id));
                            }
                        }
                    }
                    $solicitud = Solicitud::find($audiencia->expediente->solicitud_id);
                    $solicitud->update(["estatus_solicitud_id" => 3]);
                            // Se genera archivo de acta de archivado
                    $response = array("tipo" => 1,"response" => $audiencia);
//                    DB::rollBack();
//                    dd($solicitantes);
                    }
                if ($solicitantes && !$citados) {
                    $audiencia->update(array("resolucion_id" => 3, "finalizada" => true));
                    $solicitados = $this->getSolicitados($audiencia);
                    $tipo_notificacion = 1;
                    foreach($audiencia->audienciaParte as $parte){
                        if($parte->parte->tipo_parte_id == 2){
                            $tipo_notificacion = $parte->tipo_notificacion_id;
                        }
                    }
                    if($tipo_notificacion != 1){
                        /*
                         * Aqui se cumple el caso 3 dónde no acudieron las partes citadas con notificador
                         * Se gernerarán tres cosas
                         * - Constancia de no conciliación para cada parte
                         * - Acta de multa para todos los citados
                         * - Orden de notificación de multa(Pendiente)
                         */
                        $solicitantesPartes = $this->getSolicitantes($audiencia);
                        $arrayMultado = [];
                        foreach ($solicitantesPartes as $solicitante) {
                            foreach ($solicitados as $solicitado) {
                                $resolucionParte = ResolucionPartes::create([
                                            "audiencia_id" => $audiencia->id,
                                            "parte_solicitante_id" => $solicitante->parte_id,
                                            "parte_solicitada_id" => $solicitado->parte_id,
                                            "terminacion_bilateral_id" => 5
                                ]);
                                //Se genera constancia de no conciliacion
                                event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 17, 1, $solicitante->parte_id, $solicitado->parte_id));
                                $multable = false;
                                $audienciaParte = AudienciaParte::where('audiencia_id',$audiencia->id)->where('parte_id',$solicitado->parte_id)->first();
                                if( $audienciaParte && $audienciaParte->finalizado ==  "FINALIZADO EXITOSAMENTE"){
                                    if(array_search($solicitado->parte_id,$arrayMultado) === false && $audiencia->expediente->solicitud->tipo_solicitud_id == 1){
                                        // Se genera archivo de acta de multa
                                        event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 18, 7, null, $solicitado->parte_id));
                                        array_push($arrayMultado,$solicitado->parte_id);
                                    }
                                }
                            }
                        }
                        $etapa = \App\EtapaNotificacion::where("etapa","ilike","Multa")->first();
                        $audiencia->update(["etapa_notificacion_id" => $etapa->id]);
                        $citatorio = false;
                        $notificar = true;
                        $audiencia_notificar_id = $audiencia->id;
                        $response = array("tipo" => 2,"response" => $audiencia);
                    }else{
                        /*
                         * Aqui se cumple el caso 2 dónde no acudieron las partes citadas con solicitante
                         * Se generará una nueva audiencia entre 15 y 18 días hábiles
                         * Se generará una cita con notificador para entregar los nuevos citatorios
                         * Se generará una nuevo acuse
                         */
                        // buscamos disponibilidad en los proximos 15 a 18 días
                        $diasHabilesMin = 15;
                        $diasHabilesMax = 18;
                        // validamos si se debe asignar solo una sala o dos y buscamos la disponibilidad
                        if($audiencia->multiple){
                            $datos_audiencia = FechaAudienciaService::proximaFechaCitaDoble($audiencia->fecha_audiencia, auth()->user()->centro,$diasHabilesMin,$diasHabilesMax);
                            $multiple = true;
                        }else{
                            $datos_audiencia = FechaAudienciaService::proximaFechaCita($audiencia->fecha_audiencia, auth()->user()->centro,$diasHabilesMin,$diasHabilesMax);
                            $multiple = false;
                        }
                        //Obtenemos el contador
                        $ContadorController = new ContadorController();
                        $folioAudiencia = $ContadorController->getContador(3, auth()->user()->centro_id);
                        $etapa = \App\EtapaNotificacion::where("etapa","ilike","No comparecio el citado")->first();
                        //creamos el registro de la audiencia
                        $audienciaN = Audiencia::create([
                            "expediente_id" => $audiencia->expediente_id,
                            "multiple" => $audiencia->multiple,
                            "fecha_audiencia" => $datos_audiencia["fecha_audiencia"],
                            "hora_inicio" => $datos_audiencia["hora_inicio"],
                            "hora_fin" => $datos_audiencia["hora_fin"],
                            "conciliador_id" =>  $datos_audiencia["conciliador_id"],
                            "numero_audiencia" => 2,
                            "reprogramada" => false,
                            "anio" => $folioAudiencia->anio,
                            "folio" => $folioAudiencia->contador,
                            "encontro_audiencia" => $datos_audiencia["encontro_audiencia"],
                            "etapa_notificacion_id" => $etapa->id
                        ]);
                        if($datos_audiencia["encontro_audiencia"]){
                            // guardamos la sala y el consiliador a la audiencia
                            ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $datos_audiencia["conciliador_id"],"solicitante" => true]);
                            SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $datos_audiencia["sala_id"],"solicitante" => true]);
                            if($audienciaN->multiple){
                                ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $datos_audiencia["conciliador2_id"],"solicitante" => false]);
                                SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $datos_audiencia["sala2_id"],"solicitante" => false]);
                            }
                        }
                        // Guardamos todas las Partes en la audiencia
                        $acuse = Documento::where('documentable_type','App\Solicitud')->where('documentable_id',$audienciaN->expediente->solicitud_id)->where('clasificacion_archivo_id',40)->first();
                        if($acuse != null){
                            $acuse->delete();
                        }
                        foreach($audiencia->audienciaParte as $parte){
                            AudienciaParte::create(["audiencia_id" => $audienciaN->id,"parte_id" => $parte->parte_id,"tipo_notificacion_id" => 3]);
                            if($parte->parte->tipo_parte_id == 2){
                                event(new GenerateDocumentResolution($audienciaN->id,$audienciaN->expediente->solicitud_id,14,4,null,$parte->parte_id));
                            }
                        }

                        event(new GenerateDocumentResolution("",$audienciaN->expediente->solicitud_id,40,6));
                        foreach($audienciaN->salasAudiencias as $sala){
                            $sala->sala;
                        }
                        foreach($audienciaN->conciliadoresAudiencias as $conciliador){
                            $conciliador->conciliador->persona;
                        }
                        $audiencia_notificar_id = $audienciaN->id;
                        $notificar = true;
                        $citatorio = true;
                        $response = array("tipo" => 3,"response" => $audienciaN);
                    }
                }
                if ($solicitantes && $citados) {
                    if($totalCitadosComparecen >= $totalCitados){
                        /*
                         * Aquí aplica el caso en el que todos acuden a la audiencia y se debe celebrar sin ningun problema
                         * En esta sección permite que siga el flujo de manera normal
                         */
                    $audiencia_partes = AudienciaParte::where('audiencia_id', $audiencia_id)->get();
                    foreach ($audiencia_partes as $key => $audienciaP) {
                        if ($audienciaP->parte->tipo_parte_id == 1) {
                            $comparecio = Compareciente::where('audiencia_id', $audiencia_id)->where('parte_id', $audienciaP->parte_id)->first();
                                if ($comparecio == null && $audiencia->expediente->solicitud->tipo_solicitud_id == 1) {
                                $solicitados = $this->getSolicitados($audiencia);
                                foreach ($solicitados as $key => $solicitado) {
                                    event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 41, 8, $audienciaP->parte_id, $solicitado->parte_id));
                                }
                            }
                        }
                    }
                        $response = array("tipo" => 4,"response" => $audiencia);
                    }else{
                        /*
                         * Aquí aplica el caso cuando solo acudieron algunos se los citados y no todos
                         * Se deberá regresar un mensaje para que el citado indique si desea continuar con la audiencia
                         *
                         */
                        $response = array("tipo" => 5,"response" => null);
                    }
                }
            }

            DB::commit();
            if($notificar){
                if($citatorio){
                    event(new RatificacionRealizada($audiencia_notificar_id,"citatorio"));
                }else{
                    event(new RatificacionRealizada($audiencia_notificar_id,"multa"));
                }
            }
            return $this->sendResponse($response, 'SUCCESS');
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al registrar los comparecientes' . $e->getMessage(), 'Error');
        }
    }
    public function SolicitarNueva(){
        /*
         * Esta funcion es para generar una nueva audiencia si el solicitante así lo desea
         */
        // buscamos disponibilidad en los proximos 15 a 18 días
        DB::beginTransaction();
        try{
            $diasHabilesMin = 15;
            $diasHabilesMax = 18;
            $audiencia = Audiencia::find($this->request->audiencia_id);
            // validamos si se debe asignar solo una sala o dos y buscamos la disponibilidad
            if($audiencia->multiple){
                $datos_audiencia = FechaAudienciaService::proximaFechaCitaDoble($audiencia->fecha_audiencia, auth()->user()->centro,$diasHabilesMin,$diasHabilesMax);
                $multiple = true;
            }else{
                $datos_audiencia = FechaAudienciaService::proximaFechaCita($audiencia->fecha_audiencia, auth()->user()->centro,$diasHabilesMin,$diasHabilesMax);
                $multiple = false;
            }
            //Obtenemos el contador
            $ContadorController = new ContadorController();
            $folioAudiencia = $ContadorController->getContador(3, auth()->user()->centro_id);
            //creamos el registro de la audiencia
            $audienciaN = Audiencia::create([
                "expediente_id" => $audiencia->expediente_id,
                "multiple" => $audiencia->multiple,
                "fecha_audiencia" => $datos_audiencia["fecha_audiencia"],
                "hora_inicio" => $datos_audiencia["hora_inicio"],
                "hora_fin" => $datos_audiencia["hora_fin"],
                "conciliador_id" =>  $datos_audiencia["conciliador_id"],
                "numero_audiencia" => 2,
                "reprogramada" => false,
                "anio" => $folioAudiencia->anio,
                "folio" => $folioAudiencia->contador,
                "encontro_audiencia" => $datos_audiencia["encontro_audiencia"]
            ]);
            if($datos_audiencia["encontro_audiencia"]){
                // guardamos la sala y el consiliador a la audiencia
                ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $datos_audiencia["conciliador_id"],"solicitante" => true]);
                SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $datos_audiencia["sala_id"],"solicitante" => true]);
                if($audienciaN->multiple){
                    ConciliadorAudiencia::create(["audiencia_id" => $audienciaN->id, "conciliador_id" => $datos_audiencia["conciliador2_id"],"solicitante" => false]);
                    SalaAudiencia::create(["audiencia_id" => $audienciaN->id, "sala_id" => $datos_audiencia["sala2_id"],"solicitante" => false]);
                }
            }
            // Guardamos todas las Partes en la audiencia
            $acuse = Documento::where('documentable_type','App\Solicitud')->where('documentable_id',$audienciaN->expediente->solicitud_id)->where('clasificacion_archivo_id',40)->first();
            if($acuse != null){
                $acuse->delete();
            }
            foreach($audiencia->audienciaParte as $parte){
                AudienciaParte::create(["audiencia_id" => $audienciaN->id,"parte_id" => $parte->parte_id,"tipo_notificacion_id" => 3]);
                if($parte->parte->tipo_parte_id == 2){
                    event(new GenerateDocumentResolution($audienciaN->id,$audienciaN->expediente->solicitud_id,14,4,null,$parte->parte_id));
                }
            }

            event(new GenerateDocumentResolution("",$audienciaN->expediente->solicitud_id,40,6));
            foreach($audienciaN->salasAudiencias as $sala){
                $sala->sala;
            }
            foreach($audienciaN->conciliadoresAudiencias as $conciliador){
                $conciliador->conciliador->persona;
            }
            $response = array("tipo" => 3,"response" => $audienciaN);
            DB::commit();
            return $this->sendResponse($response, 'SUCCESS');
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al crear la nueva audiencia' . $e->getMessage(), 'Error');
        }
    }

    public function getComparecientes() {
        $audiencia = Audiencia::find($this->request->audiencia_id);
        $comparecientes = array();
        $solicitantes = false;
        $citados = false;
        foreach ($audiencia->comparecientes as $key => $compareciente) {
            $compareciente->parte = $compareciente->parte;
            $compareciente->documentos = $compareciente->parte->documentos;
            $parteRep = [];
            if ($compareciente->parte->tipo_parte_id == 3 && $compareciente->parte->parte_representada_id != null) {
                $parteRep = Parte::find($compareciente->parte->parte_representada_id);

                if($parteRep->tipo_parte_id == 1){
                    $solicitantes = true;
                }else{
                    if($parteRep->tipo_parte_id == 2){
                        $citados = true;
                    }
                }
            }
            $compareciente->parte->parteRepresentada = $parteRep;
            $compareciente->parte->tipoParte = TipoParte::find($compareciente->parte->tipo_parte_id)->nombre;
            if($compareciente->parte->tipo_parte_id == 1){
                $solicitantes = true;
            }else{
                if($compareciente->parte->tipo_parte_id == 2){
                    $citados = true;
                }
            }
            $comparecientes[$key] = $compareciente;
        }
        if(count($comparecientes) > 0){
            $comparecientes[0]["citados"] = $citados;
            $comparecientes[0]["solicitantes"] = $solicitantes;
        }
        return $comparecientes;
    }

    public function uploadJustificante(Request $request) {
        DB::beginTransaction();
        try {
            $audiencia = Audiencia::find($request->audiencia_id);
            $audiencia->update(["solictud_cancelcacion" => true]);
            $directorio = 'expedientes/' . $audiencia->expediente_id . '/audiencias/' . $request->audiencia_id;
            Storage::makeDirectory($directorio);
            $archivo = $request->file('justificante');
            $tipoArchivo = ClasificacionArchivo::find(7);
//            DB::rollback();
//            dd($archivos);
//            foreach($archivos as $archivo) {
            $path = $archivo->store($directorio);
            $uuid = Str::uuid();
            $audiencia->documentos()->create([
                "nombre" => str_replace($directorio . "/", '', $path),
                "nombre_original" => str_replace($directorio, '', $archivo->getClientOriginalName()),
                "descripcion" => "Justificante " . $tipoArchivo->nombre,
                "ruta" => $path,
                "uuid" => $uuid,
                "tipo_almacen" => "local",
                "uri" => $path,
                "longitud" => round(Storage::size($path) / 1024, 2),
                "firmado" => "false",
                "clasificacion_archivo_id" => 7,
            ]);
//            }
            DB::commit();
            return redirect()->back()->with('success', 'Se solicitó la cancelación');
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
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
    public function cacheModel($nombre, $modelo) {
        if (!Cache::has($nombre)) {
            $respuesta = array_pluck($modelo::all(), 'nombre', 'id');
            Cache::forever($nombre, $respuesta);
        } else {
            $respuesta = Cache::get($nombre);
        }
        return $respuesta;
    }

    public function negarCancelacion() {
        $audiencia = Audiencia::find($this->request->audiencia_id);
        $audiencia->update(["cancelacion_atendida" => true]);
        return $audiencia;
    }

    public function cambiarFecha() {
        try {
            DB::beginTransaction();
            $audiencia = Audiencia::find($this->request->audiencia_id);
            $fecha = new \Carbon\Carbon($this->request->fecha_audiencia);
            $audiencia->update(["fecha_audiencia" =>$fecha->format("Y-m-d"), "hora_inicio" => $this->request->hora_inicio, "hora_fin" => $this->request->hora_fin, "cancelacion_atendida" => true,"encontro_audiencia" => true]);
            if(isset($this->request->agregarConciliador)){
                if($this->request->agregarConciliador == 'noEncontrados'){
                    $id_conciliador = null;
                    foreach($audiencia->conciliadoresAudiencias as $conciliador){
                        $conciliador->delete();
                    }
                    foreach($audiencia->salasAudiencias as $sala){
                        $sala->delete();
                    }
                    foreach ($this->request->asignacion as $value) {
                        if ($value["resolucion"]) {
                            $id_conciliador = $value["conciliador"];
                        }
                        ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $value["conciliador"], "solicitante" => $value["resolucion"]]);
                        SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $value["sala"], "solicitante" => $value["resolucion"]]);
                    }
                    //Agregamos el la etapa de notificación
                    $etapa = \App\EtapaNotificacion::where("etapa","ilike","Cambio de Fecha")->first();
                    $audiencia->update(["conciliador_id" => $id_conciliador,"reprogramada" => true,"etapa_notificacion_id" => $etapa->id]);
                }
            }
            // generar citatorio de conciliacion
            $partes = $audiencia->expediente->solicitud->partes;
            foreach($partes as $parte){
                if($parte->tipo_parte_id == 2){
                    event(new GenerateDocumentResolution($audiencia->id,$audiencia->expediente->solicitud->id,14,4,null,$parte->id));
                }
            }
            DB::commit();
            $sin_contactar = self::NotificarCambioFecha($audiencia);
            return array("sin_contactar" => $sin_contactar);
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Algo salio mal al tratar de reagendar', 'Error');
        }
    }
    public function NotificarCambioFecha($audiencia){
        //event(new GenerateDocumentResolution($audiencia->id, $audiencia->expediente->solicitud->id, 14, 4));
        //Buscamos un correo electronico de las partes solicitadas
        $contactados = array();
        $sin_contactar = array();
        try{
            foreach($audiencia->audienciaParte as $parte){
                    if($parte->parte->contactos != null){
                        $envio = false;
                        foreach($parte->parte->contactos as $contacto){
                            $tipo_mail = TipoContacto::where("nombre","EMAIL")->first();
                            if($contacto->tipo_contacto_id == $tipo_mail->id){
    //                            Se envia la notificación por correo electronico
                                Mail::to($contacto->contacto)->send(new CambioFecha($audiencia,$parte->parte));
                                $envio = true;
                            }
                        }
                        if(!$envio){
                            $parte->parte->contactos = $parte->parte->contactos;
                            foreach($parte->parte->contactos as $contactos){
                                $contactos->tipo_contacto = $contactos->tipo_contacto;
                            }
                            array_push($sin_contactar,$parte->parte);
                        }
                    }else{
                        $parte->parte->contactos = $parte->parte->contactos;
                        foreach($parte->parte->contactos as $contactos){
                            $contactos->tipo_contacto = $contactos->tipo_contacto;
                        }
                        array_push($sin_contactar,$parte->parte);
                    }
            }
            return $sin_contactar;
        }catch(\Throwable $e){
            return $sin_contactar;
        }
    }

    public function getMinMaxTime(Centro $centro) {
//        Recorremos las disponibilidades
        $horaIniciopiv = "23:59:59";
        $horaFinpiv = "00:00:00";
        foreach ($centro->disponibilidades as $disponibilidad) {
            if ($disponibilidad->hora_inicio < $horaIniciopiv) {
                $horaIniciopiv = $disponibilidad->hora_inicio;
            }
            if ($disponibilidad->hora_fin > $horaIniciopiv) {
                $horaFinpiv = $disponibilidad->hora_fin;
            }
        }
        return array("hora_inicio" => $horaIniciopiv, "hora_fin" => $horaFinpiv);
    }

    public function getMinMaxConciliador(Conciliador $conciliador) {
//        Recorremos las disponibilidades
        $horaIniciopiv = "23:59:59";
        $horaFinpiv = "00:00:00";
        foreach ($conciliador->disponibilidades as $disponibilidad) {
            if ($disponibilidad->hora_inicio < $horaIniciopiv) {
                $horaIniciopiv = $disponibilidad->hora_inicio;
            }
            if ($disponibilidad->hora_fin > $horaIniciopiv) {
                $horaFinpiv = $disponibilidad->hora_fin;
            }
        }
        return array("hora_inicio" => $horaIniciopiv, "hora_fin" => $horaFinpiv);
    }

    public function getFullAudiencia() {
        $audiencia = Audiencia::find($this->request->audiencia_id);
        //obtenemos las salas donde se celebrará la audiencia
        foreach ($audiencia->salasAudiencias as $sala) {
            $sala->sala;
        }
        //obtenemos los conciliadores que celebrarán la audiencia
        foreach ($audiencia->conciliadoresAudiencias as $conciliador) {
            $conciliador->conciliador->persona;
        }
        //obtenemos las partes citadas a la audiencia
        foreach ($audiencia->audienciaParte as $partes) {
            $partes->parte->tipoParte;
        }
        return $audiencia;
    }
    
    public function CambiarConciliador(){
        $audiencia = Audiencia::find($this->request->audiencia_id);
        $conciliador_id = null;
        if($this->request->tipoAsignacion == 1){
            $aud = ConciliadorAudiencia::where("audiencia_id",$audiencia->id)->where("solicitante",true)->first();
            foreach ($this->request->asignacion as $value) {
                if($value["resolucion"]){
                    $conciliador_id = $value["conciliador"];
                    $aud->update(["conciliador_id" => $value["conciliador"], "solicitante" => $value["resolucion"]]);
                }
            }
        }else{
            $aud = ConciliadorAudiencia::where("audiencia_id",$audiencia->id)->where("solicitante",true)->first();
            foreach ($this->request->asignacion as $value) {
                if($value["resolucion"]){
                    $conciliador_id = $value["conciliador"];
                    $aud->update(["conciliador_id" => $value["conciliador"], "solicitante" => $value["resolucion"]]);
                }
            }
            $aud = ConciliadorAudiencia::where("audiencia_id",$audiencia->id)->where("solicitante",false)->first();
            foreach ($this->request->asignacion as $value) {
                if(!$value["resolucion"]){
                    $aud->update(["conciliador_id" => $value["conciliador"], "solicitante" => $value["resolucion"]]);
                }
            }
            
        }
        $audiencia->update(["conciliador_id" => $conciliador_id]);
        return $audiencia;
    }

    public function renderPDF($html, $plantilla_id, $path=null){
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setOption('page-size', 'Letter')
            ->setOption('margin-top', '25mm')
            ->setOption('margin-bottom', '11mm')
            ->setOption('header-html', env('APP_URL').'/header/'.$plantilla_id)
            ->setOption('footer-html', env('APP_URL').'/footer/'.$plantilla_id)
        ;
        if($path){
            return $pdf->generateFromHtml($html, $path);
        }
        return $pdf->inline();
    }
    public function renderPDFCustom($html, $htmlHeader, $path=null){
        $html=$htmlHeader.$html;
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setOption('page-size', 'Letter')
            ->setOption('margin-top', '25mm')
            ->setOption('margin-bottom', '11mm')
            ->setOption('header-html', env('APP_URL').'/header/'."1")
            ->setOption('footer-html', env('APP_URL').'/footer/'."1")
        ;
        if($path){
            return $pdf->generateFromHtml($html, $path);
        }
        return $pdf->inline();
    }
    function eliminar_acentos($cadena){

		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);

		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );

		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );

		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );

		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );

		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);

		return $cadena;
	}

}
