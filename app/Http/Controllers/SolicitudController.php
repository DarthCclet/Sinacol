<?php

namespace App\Http\Controllers;

use App\Centro;
use App\DatoLaboral;
use App\Domicilio;
use App\Estado;
use App\Expediente;
use App\Audiencia;
use App\ClasificacionArchivo;
use App\Conciliador;
use App\Contacto;
use App\EstatusSolicitud;
use App\Events\GenerateDocumentResolution;
use Illuminate\Http\Request;
use \App\Solicitud;
use Validator;
use App\Filters\SolicitudFilter;
use App\Genero;
use App\GiroComercial;
use App\GrupoPrioritario;
use App\Jornada;
use App\LenguaIndigena;
use App\MotivoExcepcion;
use App\Municipio;
use App\Nacionalidad;
use App\ObjetoSolicitud;
use App\Ocupacion;
use App\Parte;
use App\Periodicidad;
use App\ResolucionParteExcepcion;
use App\Rules\Curp;
use App\Rules\RFC;
use App\TipoAsentamiento;
use App\TipoContacto;
use App\TipoVialidad;
use App\User;
use App\Sala;
use App\SalaAudiencia;
use App\ConciliadorAudiencia;
use App\AudienciaParte;
use App\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\FechaAudienciaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Events\RatificacionRealizada;
use Carbon\Carbon;

class SolicitudController extends Controller {

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
    public function index() {
        // Filtramos los usuarios con los parametros que vengan en el request
        $solicitud = (new SolicitudFilter(Solicitud::query(), $this->request))
                ->searchWith(Solicitud::class)
                ->filter(false);

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $solicitud = $solicitud->get();
        } else {
            $filtrarCentro = true;
            $length = $this->request->get('length');
            $start = $this->request->get('start');
            $limSup = " 23:59:59";
            $limInf = " 00:00:00";
            if ($this->request->get('fechaRatificacion')) {
                $solicitud->where('fecha_ratificacion', "<", $this->request->get('fechaRatificacion') . $limSup);
                $solicitud->where('fecha_ratificacion', ">", $this->request->get('fechaRatificacion') . $limInf);
            }
            if ($this->request->get('fechaRecepcion')) {
                $solicitud->where('fecha_recepcion', "<", $this->request->get('fechaRecepcion') . $limSup);
                $solicitud->where('fecha_recepcion', ">", $this->request->get('fechaRecepcion') . $limInf);
            }
            if ($this->request->get('fechaConflicto')) {
                $solicitud->where('fecha_conflicto', $this->request->get('fechaConflicto'));
            }
            if ($this->request->get('folio')) {
                $solicitud->where('folio', $this->request->get('folio'));
                $filtrarCentro = false;
            }
            if ($this->request->get('curp')) {
                $curp = $this->request->get('curp');
                $solicitud = $solicitud->whereHas('partes', function (Builder $query) use ($curp){
                    $query->where('curp', [$curp]);
                });
                $filtrarCentro = false;
            }
           
            if ($this->request->get('nombre')) {
                $nombre = $this->request->get('nombre');
                $nombre = trim($nombre);
                $nombre = str_replace(' ','&',$nombre);
                // dd($nombre);
                $sql = " ";
                $solicitud = $solicitud->whereHas('partes', function (Builder $query) use ($nombre,$sql){
                    $query->where('tipo_parte_id',1)->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre]);
                });
            }
            
            if ($this->request->get('anio')) {
                $solicitud->where('anio', $this->request->get('anio'));
            }
            if ($this->request->get('estatus_solicitud_id')) {
                $solicitud->where('estatus_solicitud_id', $this->request->get('estatus_solicitud_id'));
            }
            if ($this->request->get('loadPartes')) {
                $solicitud = $solicitud->with("partes");
            }
            if ($this->request->get('loadPartes')) {
                $solicitud = $solicitud->with("expediente");
            }
            if ($this->request->get('Expediente')) {
                $expediente = $this->request->get('Expediente');
                // $expediente = Expediente::where('folio', $this->request->get('Expediente'))->first();
                $solicitud = $solicitud->whereHas('expediente', function (Builder $query) use ($expediente){
                    $query->where('folio', [$expediente]);
                });
                $filtrarCentro = false;
            }
            if(Auth::user()->hasRole('Orientador Central')){
                $solicitud->where('tipo_solicitud_id',3)->orWhere('tipo_solicitud_id',4);
                $filtrarCentro = false;
            }
            if($filtrarCentro){
                $centro_id = Auth::user()->centro_id;
                $solicitud->where('centro_id',$centro_id);
            }
            if ($this->request->get('IsDatatableScroll')) {
                $solicitud = $solicitud->orderBy("fecha_recepcion", 'desc')->take($length)->skip($start)->get(['id','estatus_solicitud_id','folio','anio','fecha_ratificacion','fecha_recepcion','fecha_conflicto','centro_id']);
            } else {
                $solicitud = $solicitud->paginate($this->request->get('per_page', 10));
            }
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $solicitud = tap($solicitud)->each(function ($solicitud) {
            $solicitud->loadDataFromRequest();
        });
        $objeto_solicitudes = $this->cacheModel('objeto_solicitudes', ObjetoSolicitud::class);
        $estatus_solicitudes = $this->cacheModel('estatus_solicitudes', EstatusSolicitud::class);
        if ($this->request->wantsJson()) {
            if ($this->request->get('all') || $this->request->get('paginate')) {
                return $this->sendResponse($solicitud, 'SUCCESS');
            } else {
                if($filtrarCentro){
                    $centro_id = Auth::user()->centro_id;
                    $total = Solicitud::where('centro_id',$centro_id)->count();
                }else{
                    $total = Solicitud::count();
                }
                $draw = $this->request->get('draw');
                
                return $this->sendResponseDatatable($total, $total, $draw, $solicitud, null);
            }
        }
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        return view('expediente.solicitudes.index', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes','clasificacion_archivos_Representante','clasificacion_archivo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tipo_solicitud_id = isset($this->request->solicitud) ?$this->request->solicitud : 1;
        if($tipo_solicitud_id == 1){
            $tipo_objeto_solicitudes_id = 1;
        }else if($tipo_solicitud_id == 2){
            $tipo_objeto_solicitudes_id = 2;
        }else{
            $tipo_objeto_solicitudes_id = 3;

        }
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::where('tipo_objeto_solicitudes_id',$tipo_objeto_solicitudes_id)->get(),'nombre','id');
        $estatus_solicitudes = $this->cacheModel('estatus_solicitudes',EstatusSolicitud::class);
        $tipos_vialidades = $this->cacheModel('tipos_vialidades',TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos',TipoAsentamiento::class);
        $estados = $this->cacheModel('estados',Estado::class);
        $jornadas = $this->cacheModel('jornadas',Jornada::class);
        $nacionalidades = $this->cacheModel('nacionalidades',Nacionalidad::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales',GiroComercial::class);
        $ocupaciones = $this->cacheModel('ocupaciones',Ocupacion::class);
        $grupos_prioritarios = $this->cacheModel('grupo_prioritario',GrupoPrioritario::class);
        $lengua_indigena = $this->cacheModel('lengua_indigena',LenguaIndigena::class);
        $generos = $this->cacheModel('generos',Genero::class);
        $tipo_contacto = $this->cacheModel('tipo_contacto',TipoContacto::class);
        $periodicidades = $this->cacheModel('periodicidades',Periodicidad::class);
        $motivo_excepcion = $this->cacheModel('motivo_excepcion',MotivoExcepcion::class);
        
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $giros = GiroComercial::where("parent_id",1)->orderBy('nombre')->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        // $municipios = $this->cacheModel('municipios',Municipio::class,'municipio');
        //$municipios = array_pluck(Municipio::all(),'municipio','id');
        $municipios=[];
        return view('expediente.solicitudes.create', compact('objeto_solicitudes','estatus_solicitudes','tipos_vialidades','tipos_asentamientos','estados','jornadas','generos','nacionalidades','giros_comerciales','ocupaciones','lengua_indigena','tipo_contacto','periodicidades','municipios','grupos_prioritarios','motivo_excepcion','clasificacion_archivo','tipo_solicitud_id','clasificacion_archivos_Representante','giros'));
    }
    /**
     * Función para almacenar catalogos (nombre,id) en cache
     *
     * @param [string] $nombre
     * @param [Model] $modelo
     * @return void
     */
    private function cacheModel($nombre,$modelo,$campo = 'nombre' ){
        if (!Cache::has($nombre)) {
            $respuesta = array_pluck($modelo::all(),$campo,'id');
            Cache::forever($nombre, $respuesta);
        } else {
            $respuesta = Cache::get($nombre);
        }
        return $respuesta;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $solicitud = $request->input('solicitud');
        if($solicitud["tipo_solicitud_id"] == 1){
            $request->validate([
                'solicitud.fecha_conflicto' => 'required',
                'solicitud.solicita_excepcion' => 'required',
                'solicitud.tipo_solicitud_id' => 'required',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable', new RFC],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.dato_laboral' => 'required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable', new RFC],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]);
        }else{
            $request->validate([
                'solicitud.fecha_conflicto' => 'required',
                'solicitud.solicita_excepcion' => 'required',
                'solicitud.tipo_solicitud_id' => 'required',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable', new RFC],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable', new RFC],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]); 
        }

        DB::beginTransaction();
        $domiciliop ="";
        try {
            // Solicitud
            $solicitud['user_id'] = 1;
            $solicitud['estatus_solicitud_id'] = 1;
            if(!isset($solicitud['tipo_solicitud_id'])){
                $solicitud['tipo_solicitud_id'] = 1;
            }
            $tipo_solicitud_id = $solicitud['tipo_solicitud_id'];
            $date = new \DateTime();
            $solicitud['fecha_recepcion'] = $date->format('Y-m-d H:i:s');
            $solicitud['centro_id'] = $this->getCentroId();
            //Obtenemos el contador
            $ContadorController = new ContadorController();
            $folio = $ContadorController->getContador(1, 1);
            $solicitud['folio'] = $folio->contador;
            $solicitud['anio'] = $folio->anio;
            $solicitud['ratificada'] = false;
            $solicitudSaved = Solicitud::create($solicitud);
            $objeto_solicitudes = $request->input('objeto_solicitudes');

            foreach ($objeto_solicitudes as $key => $value) {
                $solicitudSaved->objeto_solicitudes()->attach($value['objeto_solicitud_id']);
            }

            $solicitantes = $request->input('solicitantes');

            $centro = null;

            foreach ($solicitantes as $key => $value) {
                $value['solicitud_id'] = $solicitudSaved['id'];
                unset($value['activo']);
                if(isset($value['dato_laboral'])){
                    $dato_laboral = $value['dato_laboral'];
                    unset($value['dato_laboral']);
                }

                if (isset($value["domicilios"])) {
                    $domicilio = $value["domicilios"][0];
                    unset($value['domicilios']);
                }
                $contactos = [];
                if (isset($value["contactos"])) {
                    $contactos = $value["contactos"];
                    unset($value['contactos']);
                }

                // dd($value);
                $parteSaved = Parte::create($value);
                if(isset($dato_laboral)){
                    $parteSaved->dato_laboral()->create($dato_laboral);
                }
                // dd($domicilio);
                // foreach ($domicilios as $key => $domicilio) {
                unset($domicilio['activo']);
                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                if($key == 0 && ($tipo_solicitud_id == 2 ||$tipo_solicitud_id == 3 )){
                    $domiciliop = $domicilio["estado_id"];
                    $centro = $this->getCentroId($domicilio["estado_id"]);
                }
                // }
                if (count($contactos) > 0) {
                    foreach ($contactos as $key => $contacto) {
                        unset($contacto['activo']);
                        $contactoSaved = $parteSaved->contactos()->create($contacto);
                    }
                }
            }



            $solicitados = $request->input('solicitados');

            foreach ($solicitados as $key => $value) {
                unset($value['activo']);
                $domicilios = Array();
                if (isset($value["domicilios"])) {
                    $domicilios = $value["domicilios"];
                    unset($value['domicilios']);
                    if($key == 0 && ($tipo_solicitud_id == 1 ||$tipo_solicitud_id == 4 )){
                        $domiciliop = $domicilios[0]["estado_id"];
                        $centro = $this->getCentroId($domicilios[0]["estado_id"]);
                    }
                }
                if (isset($value["contactos"])) {
                    $contactos = $value["contactos"];
                    unset($value['contactos']);
                }

                $value['solicitud_id'] = $solicitudSaved['id'];
                $parteSaved = Parte::create($value);
                if (count($domicilios) > 0) {
                    foreach ($domicilios as $key => $domicilio) {
                        unset($domicilio['activo']);
                        $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                    }
                }
                if (count($contactos) > 0) {
                    foreach ($contactos as $key => $contacto) {
                        unset($contacto['activo']);
                        $contactoSaved = $parteSaved->contactos()->create($contacto);
                    }
                }
            }
            if($centro != null){
                $solicitudSaved->update(["centro_id" => $centro]);
            }

            // // Para cada objeto obtenido cargamos sus relaciones.
            $solicitudSaved = tap($solicitudSaved)->each(function ($solicitudSaved) {
                $solicitudSaved->loadDataFromRequest();
            });
            DB::commit();
            // generar acuse de solicitud
            event(new GenerateDocumentResolution("",$solicitudSaved->id,40,6));
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al crear la solicitud'.$e->getMessage(), 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al crear la solicitud');
        }
        if ($this->request->wantsJson()) {
            return $this->sendResponse($solicitudSaved, 'SUCCESS');
        }
        return redirect('solicitudes')->with('success', 'Se ha creado la solicitud exitosamente');
    }

    /**
     * Funcion para obtener el centro asignado
     *
     * @return int
     */
    private function getCentroId($estado_id = null) {
        if($estado_id != null){
            $centro = Centro::find($estado_id);
        }else{
            $centro = Centro::inRandomOrder()->first();
        }
        return $centro->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $solicitud = Solicitud::find($id);
        $parte = Parte::all()->where('solicitud_id', $solicitud->id);

        $partes = $solicitud->partes()->get(); //->where('tipo_parte_id',3)->get()->first()

        $solicitantes = $partes->where('tipo_parte_id', 1);

        foreach ($solicitantes as $key => $value) {
            $value->dato_laboral;
            $value->domicilios;
            $value->contactos;
            $solicitantes[$key]["activo"] = 1;
        }
        $solicitados = $partes->where('tipo_parte_id', 2);
        foreach ($solicitados as $key => $value) {
            $value->domicilios;
            $value->contactos;
            $solicitados[$key]["activo"] = 1;
        }
        $solicitud->objeto_solicitudes;
        $solicitud["solicitados"] = $solicitados;
        $solicitud["solicitantes"] = $solicitantes;
        $solicitud->expediente = $solicitud->expediente;
        $solicitud->giroComercial = $solicitud->giroComercial;
        $solicitud->estatusSolicitud = $solicitud->estatusSolicitud;
        $solicitud->centro = $solicitud->centro;
        if($solicitud->giroComercial){
            $solicitud->giroComercial->ambito;
        }
        return $solicitud;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $doc= [];
        $solicitud = Solicitud::find($id);
        $expediente = Expediente::where("solicitud_id", "=", $solicitud->id)->get();
        if (count($expediente) > 0) {
            $audiencias = Audiencia::where("expediente_id", "=", $expediente[0]->id)->get();
        } else {
            $audiencias = array();
        }
        $partes = array();
        foreach($solicitud->partes as $key => $parte){
            $parte->tipoParte = $parte->tipoParte;
            $parte->domicilios = $parte->domicilios()->first();
//            dd($parte);
            $partes[$key] = $parte;
        }
        
        $tipo_solicitud_id = isset($solicitud->tipo_solicitud_id) ?$solicitud->tipo_solicitud_id : 1;
        if($tipo_solicitud_id == 1){
            $tipo_objeto_solicitudes_id = 1;
        }else if($tipo_solicitud_id == 2){
            $tipo_objeto_solicitudes_id = 2;
        }else{
            $tipo_objeto_solicitudes_id = 3;

        }
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::where('tipo_objeto_solicitudes_id',$tipo_objeto_solicitudes_id)->get(),'nombre','id');
        $estatus_solicitudes = $this->cacheModel('estatus_solicitudes', EstatusSolicitud::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales', GiroComercial::class);
        $tipos_vialidades = $this->cacheModel('tipos_vialidades', TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos', TipoAsentamiento::class);
        $estados = $this->cacheModel('estados', Estado::class);
        $jornadas = $this->cacheModel('jornadas', Jornada::class);
        $generos = $this->cacheModel('generos', Genero::class);
        $nacionalidades = $this->cacheModel('nacionalidades', Nacionalidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones', Ocupacion::class);
        $grupo_prioritario = $this->cacheModel('grupo_prioritario', GrupoPrioritario::class);
        $lengua_indigena = $this->cacheModel('lengua_indigena', LenguaIndigena::class);
        $tipo_contacto = $this->cacheModel('tipo_contacto', TipoContacto::class);
        $periodicidades = $this->cacheModel('periodicidades', Periodicidad::class);
        $audits = $this->getAcciones($solicitud, $solicitud->partes, $audiencias,$expediente);
        $municipios = array_pluck(Municipio::all(),'municipio','id');
        $motivo_excepciones = $this->cacheModel('motivo_excepcion',MotivoExcepcion::class);
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        
        // dd(Conciliador::all()->persona->full_name());
        $conciliadores = array_pluck(Conciliador::with('persona')->get(),"persona.nombre",'id');
        $giros = GiroComercial::where("parent_id",1)->orderBy('nombre')->get();
        // dd($conciliador);
        // $conciliadores = $this->cacheModel('conciliadores',Conciliador::class);

        // consulta de documentos
        
        
        return view('expediente.solicitudes.edit', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes', 'tipos_vialidades', 'tipos_asentamientos', 'estados', 'jornadas', 'generos', 'nacionalidades', 'giros_comerciales', 'ocupaciones', 'expediente', 'audiencias', 'grupo_prioritario', 'lengua_indigena', 'tipo_contacto', 'periodicidades', 'audits','municipios','partes','motivo_excepciones','conciliadores','clasificacion_archivo','tipo_solicitud_id','clasificacion_archivos_Representante','giros'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function consulta($id) {
        $doc= [];
        
        //Consulta de solicitud con relaciones
        $solicitud = Solicitud::find($id);
        $parte = Parte::all()->where('solicitud_id', $solicitud->id);

        $partes = $solicitud->partes()->get(); //->where('tipo_parte_id',3)->get()->first()

        $solicitantes = $partes->where('tipo_parte_id', 1);

        foreach ($solicitantes as $key => $value) {
            $value->dato_laboral;
            $value->domicilios;
            $value->contactos;
            $solicitantes[$key]["activo"] = 1;
        }
        $solicitados = $partes->where('tipo_parte_id', 2);
        foreach ($solicitados as $key => $value) {
            $value->domicilios;
            $value->contactos;
            $solicitados[$key]["activo"] = 1;
        }
        $solicitud->objeto_solicitudes;
        $solicitud["solicitados"] = $solicitados;
        $solicitud["solicitantes"] = $solicitantes;
        $solicitud->expediente = $solicitud->expediente;
        $solicitud->giroComercial = $solicitud->giroComercial;
        if($solicitud->giroComercial){
            $solicitud->giroComercial->ambito;
        }
        //Consulta de solicitud con relaciones

        $expediente = Expediente::where("solicitud_id", "=", $solicitud->id)->get();
        if (count($expediente) > 0) {
            $audiencias = Audiencia::where("expediente_id", "=", $expediente[0]->id)->withCount('etapasResolucionAudiencia')->get();
            foreach($audiencias as $audiencia){
                foreach($audiencia->audienciaParte as $parte){
                    $documentos = $parte->documentos;
                    foreach ($documentos as $documento) {
                        $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                        $documento->tipo = pathinfo($documento->ruta)['extension'];
                        if($parte->parte->tipo_persona_id == 1){
                            $documento->parte = $parte->parte->nombre. " ".$parte->parte->primer_apellido." ".$parte->parte->segundo_apellido;
                        }else{
                            $documento->parte = $parte->parte->nombre_comercial;
                        }
                        $documento->tipo_doc = 3;
                        array_push($doc,$documento);
                    }
                }
            }
        } else {
            $audiencias = array();
        }
        $partes = array();
        foreach($solicitud->partes as $key => $parte){
            $parte->tipoParte = $parte->tipoParte;
            $parte->domicilios = $parte->domicilios()->first();
//            dd($parte);
            $partes[$key] = $parte;
            $documentos = $parte->documentos;
            foreach ($documentos as $documento) {
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta)['extension'];
                $documento->parte = $parte->nombre. " ".$parte->primer_apellido." ".$parte->segundo_apellido;
                $documento->tipo_doc = 2;
                array_push($doc,$documento);
            }
        }
        
        $tipo_solicitud_id = isset($solicitud->tipo_solicitud_id) ?$solicitud->tipo_solicitud_id : 1;
        if($tipo_solicitud_id == 1){
            $tipo_objeto_solicitudes_id = 1;
        }else if($tipo_solicitud_id == 2){
            $tipo_objeto_solicitudes_id = 2;
        }else{
            $tipo_objeto_solicitudes_id = 3;

        }
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::where('tipo_objeto_solicitudes_id',$tipo_objeto_solicitudes_id)->get(),'nombre','id');
        $estatus_solicitudes = $this->cacheModel('estatus_solicitudes', EstatusSolicitud::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales', GiroComercial::class);
        $tipos_vialidades = $this->cacheModel('tipos_vialidades', TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos', TipoAsentamiento::class);
        $estados = $this->cacheModel('estados', Estado::class);
        $jornadas = $this->cacheModel('jornadas', Jornada::class);
        $generos = $this->cacheModel('generos', Genero::class);
        $nacionalidades = $this->cacheModel('nacionalidades', Nacionalidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones', Ocupacion::class);
        $grupo_prioritario = $this->cacheModel('grupo_prioritario', GrupoPrioritario::class);
        $lengua_indigena = $this->cacheModel('lengua_indigena', LenguaIndigena::class);
        $tipo_contacto = $this->cacheModel('tipo_contacto', TipoContacto::class);
        $periodicidades = $this->cacheModel('periodicidades', Periodicidad::class);
        $audits = $this->getAcciones($solicitud, $solicitud->partes, $audiencias,$expediente);
        $municipios = array_pluck(Municipio::all(),'municipio','id');
        $motivo_excepciones = $this->cacheModel('motivo_excepcion',MotivoExcepcion::class);
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id",9)->get();
        
        // dd(Conciliador::all()->persona->full_name());
        $conciliadores = array_pluck(Conciliador::with('persona')->get(),"persona.nombre",'id');
        // dd($conciliador);
        // $conciliadores = $this->cacheModel('conciliadores',Conciliador::class);

        // consulta de documentos
        
        
        $documentos = $solicitud->documentos;
        foreach ($documentos as $documento) {
            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
            $documento->tipo = pathinfo($documento->ruta,PATHINFO_EXTENSION);
            $documento->tipo_doc = 1;
            array_push($doc,$documento);
        }
        if($solicitud->expediente && $solicitud->expediente->audiencia){
            foreach($solicitud->expediente->audiencia as $audiencia){
                $documentos = $audiencia->documentos;
                foreach ($documentos as $documento) {
                    $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                    $documento->tipo = pathinfo($documento->ruta)['extension'];
                    $documento->tipo_doc = 3;
                    $documento->audiencia = $audiencia->folio."/".$audiencia->anio;
                    $documento->audiencia_id = $audiencia->id;
                    array_push($doc,$documento);
                }
            }
        }

        $documentos = $doc;
        //termina consulta de documentos
        return view('expediente.solicitudes.consultar', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes', 'tipos_vialidades', 'tipos_asentamientos', 'estados', 'jornadas', 'generos', 'nacionalidades', 'giros_comerciales', 'ocupaciones', 'expediente', 'audiencias', 'grupo_prioritario', 'lengua_indigena', 'tipo_contacto', 'periodicidades', 'audits','municipios','partes','motivo_excepciones','conciliadores','clasificacion_archivo','tipo_solicitud_id','clasificacion_archivos_Representante','documentos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitud $solicitud) {
        if($solicitud["tipo_solicitud_id"] == 1){
            $request->validate([
                'solicitud.fecha_conflicto' => 'required',
                'solicitud.solicita_excepcion' => 'required',
                'solicitud.tipo_solicitud_id' => 'required',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable', new RFC],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.dato_laboral' => 'required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable', new RFC],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]);
        }else{
            $request->validate([
                'solicitud.fecha_conflicto' => 'required',
                'solicitud.solicita_excepcion' => 'required',
                'solicitud.tipo_solicitud_id' => 'required',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable', new RFC],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable', new RFC],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]); 
        }
        $solicitud = $request->input('solicitud');
        DB::beginTransaction();
        try {
            // Solicitud
            $solicitud['user_id'] = Auth::user()->id;
            $solicitudUp = Solicitud::find($solicitud['id']);
            $exito = $solicitudUp->update($solicitud);
            if ($exito) {
                $solicitudSaved = Solicitud::find($solicitud['id']);
            }


            $objeto_solicitudes = $request->input('objeto_solicitudes');

            $arrObjetoSolicitudes = [];
            foreach ($objeto_solicitudes as $key => $value) {
                if ($value["activo"] == 1) {
                    array_push($arrObjetoSolicitudes, $value['objeto_solicitud_id']);
                }
            }
            $solicitudSaved->objeto_solicitudes()->sync($arrObjetoSolicitudes);

            $solicitantes = $request->input('solicitantes');

            foreach ($solicitantes as $key => $value) {
                $value['solicitud_id'] = $solicitudSaved['id'];
                if ($value['activo'] == "1") {
                    unset($value['activo']);
                    if(isset($value['dato_laboral'])){
                        $dato_laboral = $value['dato_laboral'];

                        unset($value['dato_laboral']);
                    }
                    if (isset($value["domicilios"])) {
                        $domicilio = $value["domicilios"][0];
                        unset($value['domicilios']);
                    }
                    if (isset($value["contactos"])) {
                        $contactos = $value["contactos"];
                        unset($value['contactos']);
                    }


                    if (!isset($value["id"]) || $value["id"] == "") {
                        if(isset($dato_laboral)){   
                            $parteSaved = (Parte::create($value)->dato_laboral()->create($dato_laboral)->parte);
                        }
                        // foreach ($domicilios as $key => $domicilio) {

                        unset($domicilio['activo']);
                        $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                        if (count($contactos) > 0) {
                            foreach ($contactos as $key => $contacto) {
                                unset($contacto['activo']);
                                $contactoSaved = $parteSaved->contactos()->create($contacto);
                            }
                        }
                    } else {
                        $parteSaved = Parte::find($value['id']);
                        $parteUpdated = $parteSaved->update($value);
                        $parteSaved = Parte::find($value['id']);
                        if(isset($dato_laboral)){   
                            if (isset($dato_laboral["id"]) && $dato_laboral["id"] != "") {
                                $dato_laboralUp = DatoLaboral::find($dato_laboral["id"]);
                                $dato_laboralUp->update($dato_laboral);
                            } else {
                                $dato_laboral = ($parteSaved->dato_laboral()->create($dato_laboral));
                            }
                        }
                        unset($domicilio['activo']);
                        if (isset($domicilio["id"]) && $domicilio["id"] != "") {
                            $domicilioUp = Domicilio::find($domicilio["id"]);

                            $domicilioUp->update($domicilio);
                        } else {
                            $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                        }

                        foreach ($contactos as $key => $contacto) {
                            if ($contacto["id"] != "") {

                                $contactoUp = Contacto::find($contacto["id"]);

                                if (isset($contacto["activo"]) && $contacto["activo"] == 0) {
                                    $contactoUp->delete();
                                } else {
                                    unset($contacto['activo']);
                                    $contactoUp->update($contacto);
                                }
                            } else {
                                unset($contacto['activo']);
                                $contactoSaved = $parteSaved->contactos()->create($contacto);
                            }
                        }
                    }
                } else {
                    $parteSaved = Parte::find($value['id']);
                    $parteSaved = $parteSaved->delete();
                }
            }

            $solicitados = $request->input('solicitados');

            foreach ($solicitados as $key => $value) {
                if ($value['activo'] == "1") {
                    unset($value['activo']);
                    $domicilios = Array();
                    if (isset($value["domicilios"])) {
                        $domicilios = $value["domicilios"];
                        unset($value['domicilios']);
                    }
                    $contactos = Array();
                    if (isset($value["contactos"])) {
                        $contactos = $value["contactos"];
                        unset($value['contactos']);
                    }

                    $value['solicitud_id'] = $solicitudSaved['id'];
                    if (!isset($value["id"]) || $value["id"] == "") {
                        $parteSaved = Parte::create($value);
                        if (count($domicilios) > 0) {
                            foreach ($domicilios as $key => $domicilio) {
                                unset($domicilio['activo']);
                                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                            }
                        }
                        foreach ($contactos as $key => $contacto) {
                            unset($contacto['activo']);
                            $contactoSaved = $parteSaved->contactos()->create($contacto);
                        }
                    } else {
                        $parteSaved = Parte::find($value['id']);
                        $parteSaved->update($value);
                        foreach ($domicilios as $key => $domicilio) {
                            if ($domicilio["id"] != "") {
                                $domicilioUp = Domicilio::find($domicilio["id"]);
                                if (isset($domicilio["activo"]) && $domicilio["activo"] == 0) {
                                    $domicilioUp->delete();
                                } else {
                                    unset($domicilio['activo']);
                                    $domicilioUp = Domicilio::find($domicilio["id"]);
                                    $domicilioUp->update($domicilio);
                                }
                            } else {
                                unset($domicilio['activo']);
                                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                            }
                        }
                        foreach ($contactos as $key => $contacto) {
                            if ($contacto["id"] != "") {
                                $contactoUp = Contacto::find($contacto["id"]);
                                if (isset($contacto["activo"]) && $contacto["activo"] == 0) {
                                    $contactoUp->delete();
                                } else {
                                    unset($contacto['activo']);
                                    $contactoUp->update($contacto);
                                }
                            } else {
                                unset($contacto['activo']);
                                $contactoSaved = $parteSaved->contactos()->create($contacto);
                            }
                        }
                    }
                } else {
                    $parteSaved = Parte::find($value['id']);
                    $parteSaved->delete();
                }
            }

            // // Para cada objeto obtenido cargamos sus relaciones.
            $solicitudSaved = tap($solicitudSaved)->each(function ($solicitudSaved) {
                $solicitudSaved->loadDataFromRequest();
            });
            DB::commit();
        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {

                return $this->sendError('Error'.$e->getMessage());
            }
            return redirect('solicitudes')->with('error', 'Error al crear la solicitud');
        }
        if ($this->request->wantsJson()) {
            return $this->sendResponse($solicitudSaved, 'SUCCESS');
        }
//        return redirect('solicitudes')->with('success', 'Se ha creado la solicitud exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Solicitud $solicitud) {
        $solicitud->delete();
        return response()->json(null, 204);
    }

    public function ratificarIncompetencia(Request $request) {
        DB::beginTransaction();
        try{
            $solicitud = Solicitud::find($request->id);
            $ContadorController = new ContadorController();
            //Obtenemos el contador
            $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
            $edo_folio = $solicitud->centro->abreviatura;
            $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
            //Creamos el expediente de la solicitud
            $expediente = Expediente::create(["solicitud_id" => $request->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
            //ratificacion de las partes
            foreach ($solicitud->partes as $key => $parte) {
                if(count($parte->documentos) == 0){
                    $parte->ratifico = true;
                    $parte->update();
                }
            }
            $solicitud->update(["estatus_solicitud_id" => 2, "ratificada" => true, "fecha_ratificacion" => now(),"inmediata" => false]);
            
            // Obtenemos la sala virtual
            $sala_id = Sala::where("centro_id",$solicitud->centro_id)->where("virtual",true)->get()[0]->id;
            //obtenemos al conciliador disponible
            $conciliadores = Conciliador::where("centro_id",$solicitud->centro_id)->get();
            $conciliadoresDisponibles = array();
            foreach($conciliadores as $conciliador){
                $conciliadorDisponible = false;
                foreach($conciliador->rolesConciliador as $roles){
                    if($roles->rol_atencion_id == 2){
                        $conciliadorDisponible = true;
                    }
                }
                if($conciliadorDisponible){
                    $conciliadoresDisponibles[]=$conciliador;
                }
            }
            $conciliador_id = null;
            if(count($conciliadoresDisponibles) > 0){
                $conciliador = $this->array_random_assoc($conciliadoresDisponibles);
            }else{
                return $this->sendError('No hay conciliadores con rol de previo acuerdo', 'Error');
            }
            
            // Registramos la audiencia
            //Obtenemos el contador
            $folioAudiencia = $ContadorController->getContador(3, auth()->user()->centro_id);
            //creamos el registro de la audiencia
            $audiencia = Audiencia::create([
                "expediente_id" => $expediente->id,
                "multiple" => false,
                "fecha_audiencia" => now()->format('Y-m-d'),
                "hora_inicio" => now()->format('H:i:s'), 
                "hora_fin" => \Carbon\Carbon::now()->addHours(1)->format('H:i:s'),
                "conciliador_id" =>  $conciliador[0]->id,
                "numero_audiencia" => 1,
                "reprogramada" => false,
                "anio" => $folioAudiencia->anio,
                "folio" => $folioAudiencia->contador
            ]);
            
            // guardamos la sala y el conciliador a la audiencia
            ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $conciliador[0]->id,"solicitante" => true]);
            SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $sala_id,"solicitante" => true]);
            // Guardamos todas las Partes en la audiencia
            $partes = $solicitud->partes;
            foreach($partes as $parte){
                AudienciaParte::create(["audiencia_id" => $audiencia->id,"parte_id" => $parte->id,"tipo_notificacion_id" => 1]);
                if($parte->tipo_parte_id == 1){
                    //generar constancia de incompetencia por solicitante
                    event(new GenerateDocumentResolution($audiencia->id,$solicitud->id,13,10,$parte->id,null));
                }
            }
        DB::commit();
        return $solicitud;

        }catch(\Throwable $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            // dd($e);
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al ratificar la solicitud', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al ratificar la solicitud');
        }
    }
    public function Ratificar(Request $request) {
        if(!self::validarCentroAsignacion()){
            return $this->sendError('No se ha configurado el centro', 'Error');
            exit;
        }
        DB::beginTransaction();
        try{
            $solicitud = Solicitud::find($request->id);
            $ContadorController = new ContadorController();
            //Obtenemos el contador
            $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
            $edo_folio = $solicitud->centro->abreviatura;
            $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
            //Creamos el expediente de la solicitud
            $expediente = Expediente::create(["solicitud_id" => $request->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
            foreach ($solicitud->partes as $key => $parte) {
                if(count($parte->documentos) == 0){
                    $parte->ratifico = true;
                    $parte->update();
                }
            }
            if($request->inmediata == "true"){
                $solicitud->update(["estatus_solicitud_id" => 2, "ratificada" => true, "fecha_ratificacion" => now(),"inmediata" => true]);
                // Obtenemos la sala virtual
                $sala_id = Sala::where("centro_id",$solicitud->centro_id)->where("virtual",true)->get()[0]->id;
                //obtenemos al conciliador disponible
                $conciliadores = Conciliador::where("centro_id",$solicitud->centro_id)->get();
                $conciliadoresDisponibles = array();
                foreach($conciliadores as $conciliador){
                    $conciliadorDisponible = false;
                    foreach($conciliador->rolesConciliador as $roles){
                        if($roles->rol_atencion_id == 2){
                            $conciliadorDisponible = true;
                        }
                    }
                    if($conciliadorDisponible){
                        $conciliadoresDisponibles[]=$conciliador;
                    }
                }
                $conciliador_id = null;
                if(count($conciliadoresDisponibles) > 0){
                    $conciliador = $this->array_random_assoc($conciliadoresDisponibles);
                }else{
                    return $this->sendError('No hay conciliadores con rol de previo acuerdo', 'Error');
                }
                
                
                // Registramos la audiencia
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
                    "multiple" => false,
                    "fecha_audiencia" => now()->format('Y-m-d'),
                    "hora_inicio" => now()->format('H:i:s'), 
                    "hora_fin" => \Carbon\Carbon::now()->addHours(1)->format('H:i:s'),
                    "conciliador_id" =>  $conciliador[0]->id,
                    "numero_audiencia" => 1,
                    "reprogramada" => false,
                    "anio" => $folioAudiencia->anio,
                    "folio" => $folioAudiencia->contador,
                    "fecha_cita" => $fecha_cita
                ]);
                
                // guardamos la sala y el conciliador a la audiencia
                ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $conciliador[0]->id,"solicitante" => true]);
                SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $sala_id,"solicitante" => true]);
                // Guardamos todas las Partes en la audiencia
                $partes = $solicitud->partes;
                foreach($partes as $parte){
                    AudienciaParte::create(["audiencia_id" => $audiencia->id,"parte_id" => $parte->id,"tipo_notificacion_id" => 1]);
                    if($parte->tipo_parte_id == 2){
                        // generar citatorio de conciliacion
                        event(new GenerateDocumentResolution($audiencia->id,$solicitud->id,14,4,null,$parte->id));
                    }
                }
                DB::commit();
                return $audiencia;
            }else{
                if((int)$request->tipo_notificacion_id == 1){
                    $diasHabilesMin = 5;
                    $diasHabilesMax = 8;
                }else{
                    $diasHabilesMin = 15;
                    $diasHabilesMax = 18;
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
                $centroResponsable = auth()->user()->centro;
                if($solicitud->tipo_solicitud_id == 3 || $solicitud->tipo_solicitud_id == 4){
                    $centroResponsable = Centro::where("nombre","Oficina Central del CFCRL")->first();
                }
                if($request->separados == "true"){
                    $datos_audiencia = FechaAudienciaService::proximaFechaCitaDoble(date("Y-m-d"), $centroResponsable,$diasHabilesMin,$diasHabilesMax);
                    $multiple = true;
                }else{
                    $datos_audiencia = FechaAudienciaService::proximaFechaCita(date("Y-m-d"), $centroResponsable,$diasHabilesMin,$diasHabilesMax);
                    $multiple = false;
                }
//                Solicitamos la fecha limite de notificacion solo cuando el tipo de notificación es por notificador sin cita
                $fecha_notificacion = null;
                if((int)$request->tipo_notificacion_id == 2){
                    $fecha_notificacion = self::obtenerFechaLimiteNotificacion($domicilio_centro,$domicilio_citado,$datos_audiencia["fecha_audiencia"]);
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
                    "fecha_audiencia" => $datos_audiencia["fecha_audiencia"],
                    "fecha_limite_audiencia" => $fecha_notificacion,
                    "hora_inicio" => $datos_audiencia["hora_inicio"], 
                    "hora_fin" => $datos_audiencia["hora_fin"],
                    "conciliador_id" =>  $datos_audiencia["conciliador_id"],
                    "numero_audiencia" => 1,
                    "reprogramada" => false,
                    "anio" => $folioAudiencia->anio,
                    "folio" => $folioAudiencia->contador,
                    "encontro_audiencia" => $datos_audiencia["encontro_audiencia"],
                    "fecha_cita" => $fecha_cita
                ]);
//                dd($audiencia);
                if($datos_audiencia["encontro_audiencia"]){
                    // guardamos la sala y el consiliador a la audiencia
                    ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $datos_audiencia["conciliador_id"],"solicitante" => true]);
                    SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $datos_audiencia["sala_id"],"solicitante" => true]);
                    if($request->separados == "true"){
                        ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $datos_audiencia["conciliador2_id"],"solicitante" => false]);
                        SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $datos_audiencia["sala2_id"],"solicitante" => false]);
                    }
                }
                // Guardamos todas las Partes en la audiencia
                
//                dd($partes);
                $tipo_notificacion_id = null;
                foreach($partes as $parte){
                    if($parte->tipo_parte_id != 1){
                        $tipo_notificacion_id = $this->request->tipo_notificacion_id;
                    }
                    AudienciaParte::create(["audiencia_id" => $audiencia->id,"parte_id" => $parte->id,"tipo_notificacion_id" => $tipo_notificacion_id]);
                    if($parte->tipo_parte_id == 2){
                        event(new GenerateDocumentResolution($audiencia->id,$solicitud->id,14,4,null,$parte->id));
                    }
                }
                if($datos_audiencia["encontro_audiencia"] && ($tipo_notificacion_id != 1 && $tipo_notificacion_id != null)){
                    event(new RatificacionRealizada($audiencia->id,"citatorio"));
                }
                $expediente = Expediente::find($request->expediente_id);
            }
            
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
            DB::commit();
            event(new GenerateDocumentResolution("",$solicitud->id,40,6));
            return $audiencia;
        }catch(\Throwable $e){
//            dd($e);
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al ratificar la solicitud', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al ratificar la solicitud');
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al enviar las notificaciones', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al enviar las notificaciones');
        }
    }
    function array_random_assoc($arr, $num = 1) {
        $keys = array_keys($arr);
        shuffle($keys);

        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[$keys[$i]] = $arr[$keys[$i]];
        }
        return $r;
    }

    function ExcepcionConciliacion(Request $request){

        $solicitud_id = $request->solicitud_id_excepcion;
        $solicitud = Solicitud::find($solicitud_id);
        $files = $request->file();
        foreach($files as $parte_id => $archivo){
            $clasificacion_archivo = 7;
            $parte = Parte::find($parte_id);
            if($solicitud != null){
                $directorio = 'expedientes/' . $solicitud->expediente->id . '/solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                Storage::makeDirectory($directorio);
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                $path = $archivo->store($directorio);

                $parte->documentos()->create([
                    "nombre" => str_replace($directorio."/", '',$path),
                    "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                    "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                    "ruta" => $path,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => $tipoArchivo->id ,
                ]);
            }
        }

        $solicitados = Parte::where('solicitud_id',$solicitud_id)->where('tipo_parte_id',2)->get();
        foreach ($solicitados as $key => $solicitado) {
            foreach($request->files as $solicitante_id => $file){
                ResolucionParteExcepcion::create(['parte_solicitante_id'=>$solicitante_id,'parte_solicitada_id'=>$solicitado->id,'conciliador_id'=>$request->conciliador_excepcion_id,'resolucion_id'=> 3 ]);
                // generar constancia de excepcion a la conciliacion
                event(new GenerateDocumentResolution("",$solicitud_id,2,5,$solicitante_id,$solicitado->id,$request->conciliador_excepcion_id));
            }
        }
        $solicitud->estatus_solicitud_id = 3;
        $solicitud->update();
        return redirect('solicitudes')->with('success', 'Se guardo todo');
    }

    function getDocumentosSolicitud($solicitud_id) {
        $doc = [];
        $solicitud = Solicitud::find($solicitud_id);
        $documentos = $solicitud->documentos;
        foreach ($documentos as $documento) {
            if($documento->ruta != ""){
            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
            $documento->tipo = pathinfo($documento->ruta)['extension'];
            array_push($doc,$documento);
        }
        }
        $partes = Parte::where('solicitud_id',$solicitud_id)->get();
        foreach($partes as $parte){

            $documentos = $parte->documentos;
            foreach ($documentos as $documento) {
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta)['extension'];
                $documento->parte = $parte->nombre. " ".$parte->primer_apellido." ".$parte->segundo_apellido;
                array_push($doc,$documento);
            }
        }
        return $doc;
    }
    function getAcuseSolicitud($solicitud_id) {
        $doc = [];
        $solicitud = Solicitud::find($solicitud_id);
        if($solicitud != null){
            $documentos = $solicitud->documentos;
            foreach ($documentos as $documento) {
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                if($documento->clasificacionArchivo->id == 40){    
                    $documento->tipo = pathinfo($documento->ruta)['extension'];
                    array_push($doc,$documento);
                }
            }
            return $doc;
        }
        return $this->sendError('No se puede obtener el acuse', 'Error');
    }
    private function getAcciones(Solicitud $solicitud,$partes,$audiencias,$expediente){
//         Obtenemos las acciones de la solicitud
        $SolicitudAud = $solicitud->audits()->get();
//        Obtenemos las acciones de las partes
        foreach($partes as $parte){
            $SolicitudAud = $SolicitudAud->merge($parte->audits()->get());
        }
//        Obtenemos las acciones de las audiencias
        foreach($audiencias as $audiencia){
            $SolicitudAud = $SolicitudAud->merge($audiencia->audits()->get());
        }
        if (count($expediente) > 0) {
            $SolicitudAud = $SolicitudAud->merge($expediente[0]->audits()->get());
        }

        $SolicitudAud = $SolicitudAud->sortBy('created_at');
        $audits = array();
        foreach ($SolicitudAud as $audit) {
            $table = "Solicitud";
            $extra = "";
            if($audit->auditable_type == 'App\Parte'){
                $table = "Parte";
                $parte = Parte::find($audit->auditable_id);
                if($parte->tipo_persona_id == 1){
                    $extra = $parte->nombre." ".$parte->primer_apellido." ".$parte->segundo_apellido;
                }else{
                    $extra = $parte->nombre_comercial;
                }
            }else if($audit->auditable_type == 'App\Audiencia'){
                $table = "Audiencia";
            }else if($audit->auditable_type == 'App\Expediente'){
                $table = "Expediente";
                $expediente = Expediente::find($audit->auditable_id);
                $extra = $expediente->folio."/".$expediente->anio;
            }
            $nombre = "Sin dato";
            if($audit->user_id != null){
                $user = User::find($audit->user_id);
                $nombre = $user->persona->nombre." ".$user->persona->primer_apellido." ".$user->persona->segundo_apellido;
            }
            $audits[] = array("user" => $nombre, "elemento" => $table,"extra" => $extra,"event" => $audit->event, "created_at" => $audit->created_at, "cambios" => $audit->getModified());
        }
        return $audits;
     }
    public function validarCorreos(){
        $solicitud = Solicitud::find($this->request->solicitud_id);
        $array = array();
        foreach($solicitud->partes as $parte){
            if($parte->tipo_parte_id == 1){
                $pasa = false;
                foreach($parte->contactos as $contacto){
                    if($contacto->tipo_contacto_id == 3){ //si tiene email
                        $pasa = true;
                    }
                }
                if(!$pasa){//devuelve partes sin email
                    if($parte->correo_buzon == null || $parte->correo_buzon == ""){
                        $array[] = $parte;
                    }
                }
            }
        }
        return $array;
    }
    public function cargarCorreos(){
        try{
            DB::beginTransaction();
            foreach ($this->request->listaCorreos as $listaCorreos){
                $parte = Parte::find($listaCorreos["parte_id"]);
                if($listaCorreos["crearAcceso"]){
                    $arrayCorreo = $this->construirCorreo($parte);
                    $parte->update([
                        "correo_buzon" => $arrayCorreo["correo"],
                        "password_buzon" => $arrayCorreo["password"]
                    ]);
                }else{
                    $parte->contactos()->create([
                        "tipo_contacto_id" => 3,
                        "contacto" => $listaCorreos["correo"]
                    ]);
                }
            }
            DB::commit();
            return $this->sendResponse("success", "Se guardaron los correos");
        }catch(\Throwable $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al guardar los correos', 'Error');
        }
    }
    private function construirCorreo(Parte $parte){
        $password = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        if($parte->tipo_persona_id == 1){
            $correo = str_replace(' ', '', $parte->curp)."@mibuzonlaboral.gob.mx";
        }else{
            $correo = str_replace(' ', '', $parte->rfc)."@mibuzonlaboral.gob.mx";
        }
        return ["correo" => strtolower($correo),"password" => strtolower($password)];
    }
    private static function validarCentroAsignacion() {
        $pasa = false;
        $pasaSala = false;
        $pasaConciliador = false;
        if(count(auth()->user()->centro->disponibilidades) > 0){
            foreach(auth()->user()->centro->salas as $sala){
                if(count($sala->disponibilidades) > 0){
                    if(!$sala->virtual){
                        $pasaSala = true;
                    }
                }
            }
            if($pasaSala){
                foreach(auth()->user()->centro->conciliadores as $conciliador){
                    if(count($conciliador->disponibilidades) > 0){
                        $pasaConciliador = true;
                    }
                }
            }
        }
        if($pasaSala && $pasaConciliador){
            $pasa = true;
        }
        return $pasa;
    }
    private function obtenerFechaLimiteNotificacion(Domicilio $centro = null,Domicilio $domicilioCitado = null,$fecha_audiencia = null){
        if($centro != null){
    //        Obtenemos la latitud del centro
            $lat_centro = $centro->latitud;
            $lon_centro = $centro->longitud;
            $lat_citado = $domicilioCitado->latitud;
            $lon_citado = $domicilioCitado->longitud;            
        }else{
            $lat_centro = 19.3137542;
            $lon_centro = -99.6386443;
            $lat_citado = 24.852421;
            $lon_citado = -102.294305;
            $fecha_audiencia = "2020/10/16";
        }
        $sql = "select (point(".$lon_centro.",".$lat_centro.") <@> point(".$lon_citado.",".$lat_citado.")) as distancia";
        $cons = DB::select($sql);
        $con = (int)$cons[0]->distancia;
        if($con < 200){
            $dias = 5;
        }else if($con < 400){
            $dias = 8;
        }else if($con < 600){
            $dias = 9;
        }else if($con < 800){
            $dias = 10;
        }else if($con < 1000){
            $dias = 11;
        }else{
            $dias =12;
        }
        $fecha = self::ultimoDiaHabilMenosDias($fecha_audiencia, $dias);
        return $fecha;
    }
    public static function ultimoDiaHabilDesde($fecha){

        $d = new Carbon($fecha);
        $ayer = $d->subDay()->format("Y-m-d");
        if(self::esFeriado($ayer)){
            $d = new Carbon($fecha);
            $ayer = $d->subDay()->format("Y-m-d");
            return self::ultimoDiaHabilDesde($ayer);
        }
        else{
            return $ayer;
        }
    }
    public static function ultimoDiaHabilMenosDias($fecha,$dias){
        $fecha = new Carbon($fecha);
        $diasRecorridos = 1;
        while ($diasRecorridos < $dias){
            $ayer = $fecha->subDay()->format("Y-m-d");
            if(!self::esFeriado($ayer)){
                $fecha = new Carbon($ayer);
                $diasRecorridos++;
            }
        }
        return self::ultimoDiaHabilDesde($fecha);
    }
    public static function esFeriado($fecha)
    {
        $d = new Carbon($fecha);
        if($d->isWeekend()){
            return true;
        }
        return false;

//        return Feriado::whereRaw("(fecha)::date = ?",[$fecha])
//            ->first() ? true : false;

    }
}
