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
use App\TipoAsentamiento;
use App\TipoContacto;
use App\TipoVialidad;
use App\CentroMunicipio;
use App\User;
use App\Sala;
use App\SalaAudiencia;
use App\ConciliadorAudiencia;
use App\AudienciaParte;
use App\CanalFolio;
use App\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\FechaAudienciaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Events\RatificacionRealizada;
use App\TipoIncidenciaSolicitud;
use App\TipoSolicitud;
use Carbon\Carbon;
use App\Traits\FechaNotificacion;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Providers\HerramientaServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

class SolicitudController extends Controller {

    use FechaNotificacion;
    /**
     * Días para expiración de solicitudes
     */
    const DIAS_EXPIRAR = 44;

    /**
     * Instancia del request
     * @var Request
     */
    protected $request;

    public function __construct(Request $request) {
        // $this->middleware("auth");
        $this->request = $request;
    }

    public function index() {
        try {
            $centro_id = Auth::user()->centro_id;
            $mostrar_caducos = $this->request->get('alert');
            if (!$this->request->wantsJson()) {
                $caducan = HerramientaServiceProvider::getSolicitudesPorCaducar(true);
                if(count($caducan) == 0){
                    $mostrar_caducos = null;
                }
                $objeto_solicitudes = $this->cacheModel('objeto_solicitudes', ObjetoSolicitud::class);
                $estatus_solicitudes = $this->cacheModel('estatus_solicitudes', EstatusSolicitud::class);
                $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
                $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->orWhere("tipo_archivo_id", 10)->get();
                $tipo_solicitud = array_pluck(TipoSolicitud::all(), 'nombre', 'id');
                $conciliadores = Conciliador::where('centro_id', $centro_id)->with('persona')->get()->pluck('persona.FullName', 'id');
                return view('expediente.solicitudes.index', compact( 'objeto_solicitudes', 'estatus_solicitudes', 'clasificacion_archivos_Representante', 'clasificacion_archivo', 'tipo_solicitud', 'conciliadores','caducan','mostrar_caducos'));
            }
            // Filtramos los usuarios con los parametros que vengan en el request
            $solicitud = (new SolicitudFilter(Solicitud::query(), $this->request))
            ->searchWith(Solicitud::class)
            ->filter(false);
            $solicitud->whereRaw('incidencia is not true');
            $filtrarCentro = true;
            $length = $this->request->get('length');
            $start = $this->request->get('start');
            if ($this->request->get('Expediente')) {
                $filtrarCentro = false;
            }
            if (Auth::user()->hasRole('Super Usuario')) {
                $filtrarCentro = false;
            }
            if (Auth::user()->hasRole('Orientador Central')) {
                $solicitud->whereRaw('(tipo_solicitud_id = 3 or tipo_solicitud_id = 4)');
                $filtrarCentro = false;
            }
            if (Auth::user()->hasRole('Personal conciliador') && $this->request->get('mis_solicitudes') == "true") {
                $conciliador = auth()->user()->persona->conciliador;
                if ($conciliador != null) {
                    $conciliador_id = $conciliador->id;
                    $solicitud->whereHas('expediente.audiencia', function($q) use($conciliador_id) {
                        $q->where('conciliador_id', $conciliador_id);
                    });
                }
            }
            if ($filtrarCentro) {
                $solicitud->where('centro_id', $centro_id);
            }
            $filtered = $solicitud->count();
            $solicitud->with('user.persona');
            if ($this->request->get('IsDatatableScroll')) {
                $solicitud = $solicitud->orderBy("fecha_recepcion", 'desc')->take($length)->skip($start)->get(['id', 'estatus_solicitud_id', 'folio', 'anio', 'fecha_ratificacion', 'fecha_recepcion', 'fecha_conflicto', 'centro_id', 'user_id', 'virtual']);
            } else {
                $solicitud = $solicitud->paginate($this->request->get('per_page', 10));
            }
            // // Para cada objeto obtenido cargamos sus relaciones.
            $solicitud = tap($solicitud)->each(function ($solicitud) {
                $solicitud->loadDataFromRequest();
            });
            if ($this->request->get('all') || $this->request->get('paginate')) {
                return $this->sendResponse($solicitud, 'SUCCESS');
            } else {
                if ($filtrarCentro) {
                    $total = Solicitud::where('centro_id', $centro_id)->count();
                } else {
                    $total = Solicitud::count();
                }
                
                $draw = $this->request->get('draw');
                return $this->sendResponseDatatable($total, $filtered, $draw, $solicitud, null);
            }

        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            if ($this->request->wantsJson()) {
                return $this->sendResponseDatatable(0, 0, 0, [], null);
            }
            return redirect('solicitudes')->with('error', 'Error al consultar la solicitud');
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index2() {
        try {
            // Filtramos los usuarios con los parametros que vengan en el request
            $solicitud = (new SolicitudFilter(Solicitud::query(), $this->request))
                    ->searchWith(Solicitud::class)
                    ->filter(false);
            // Si en el request viene el parametro all entonces regresamos todos los elementos
            $solicitud->whereRaw('incidencia is not true');
            $mostrar_caducos = $this->request->get('alert');
            // de lo contrario paginamos
            if ($this->request->get('all')) {
                $solicitud = $solicitud->get();
            } else {
                $centro_id = Auth::user()->centro_id;
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
                }
                if ($this->request->get('curp')) {
                    $curp = $this->request->get('curp');
                    $solicitud = $solicitud->whereHas('partes', function (Builder $query) use ($curp) {
                        $query->where('curp', [$curp]);
                    });
                }

                if ($this->request->get('nombre')) {
                    $nombre = $this->request->get('nombre');
                    $nombre = trim($nombre);
                    $nombre = str_replace(' ', '&', $nombre);
                    $sql = " ";
                    $solicitud = $solicitud->whereHas('partes', function (Builder $query) use ($nombre, $sql) {
                        $query->where('tipo_parte_id', 1)->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre]);
                    });
                }
                if ($this->request->get('nombre_citado')) {
                    $nombre_citado = $this->request->get('nombre_citado');
                    $nombre_citado = trim($nombre_citado);
                    $nombre_citado = str_replace(' ', '&', $nombre_citado);
                    $sql = " ";
                    $solicitud = $solicitud->whereHas('partes', function (Builder $query) use ($nombre_citado, $sql) {
                        $query->where('tipo_parte_id', 2)->whereRaw("to_tsvector('spanish', unaccent(trim(coalesce(nombre_comercial,' ')||' '||coalesce(nombre,' ')||' '||coalesce(primer_apellido,' ')||' '||coalesce(segundo_apellido,' ')))) @@ to_tsquery('spanish', unaccent(?))", [$nombre_citado]);
                    });
                }
                if ($this->request->get('dias_expiracion')) {
                    $dias_expiracion = $this->request->get('dias_expiracion');
                    $dias_rango_inferior = self::DIAS_EXPIRAR - $dias_expiracion;
                    $dias_rango_superior = self::DIAS_EXPIRAR;
                    $fecha_fin = Carbon::now()->subDays($dias_rango_inferior);
                    $fecha_inicio = Carbon::now()->subDays($dias_rango_superior);
                    $sql = " ";
                    $solicitud = $solicitud->where('fecha_recepcion','<',$fecha_fin->toDateString())->where('estatus_solicitud_id',2);
                    $rolActual = session('rolActual')->name;
                    if($rolActual == "Personal conciliador"){
                        $conciliador_id = auth()->user()->persona->conciliador->id;
                        $solicitud = $solicitud->whereHas('expediente.audiencia',function ($query) use ($conciliador_id) { $query->where('conciliador_id',$conciliador_id); });
                    }
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
                    $solicitud = $solicitud->whereHas('expediente', function (Builder $query) use ($expediente) {
                        $query->where('folio', [$expediente]);
                    });
                    $filtrarCentro = false;
                }
                if (Auth::user()->hasRole('Super Usuario')) {
                    $filtrarCentro = false;
                }
                if (Auth::user()->hasRole('Orientador Central')) {
                    $solicitud->whereRaw('(tipo_solicitud_id = 3 or tipo_solicitud_id = 4)');
                    $filtrarCentro = false;
                }
                if (Auth::user()->hasRole('Personal conciliador') && $this->request->get('mis_solicitudes') == "true") {
                    $conciliador = auth()->user()->persona->conciliador;
                    if ($conciliador != null) {
                        $conciliador_id = $conciliador->id;
                        $solicitud->whereHas('expediente.audiencia', function($q) use($conciliador_id) {
                            $q->where('conciliador_id', $conciliador_id);
                        });
                    }
                }
                if ($this->request->get('conciliador_id')) {
                    $conciliador_id = $this->request->get('conciliador_id');
                    $solicitud->whereHas('expediente.audiencia', function($q) use($conciliador_id) {
                        $q->where('conciliador_id', $conciliador_id);
                    });
                }
                if ($this->request->get('tipo_solicitud_id')) {
                    $solicitud->where('tipo_solicitud_id', $this->request->get('tipo_solicitud_id'));
                }
                if ($filtrarCentro) {
                    $solicitud->where('centro_id', $centro_id);
                }
                $filtered = $solicitud->count();
                $solicitud->with('user.persona');
                if ($this->request->get('IsDatatableScroll')) {
                    $solicitud = $solicitud->orderBy("fecha_recepcion", 'desc')->take($length)->skip($start)->get(['id', 'estatus_solicitud_id', 'folio', 'anio', 'fecha_ratificacion', 'fecha_recepcion', 'fecha_conflicto', 'centro_id', 'user_id', 'virtual']);
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
            $caducan = HerramientaServiceProvider::getSolicitudesPorCaducar(true);
            if(count($caducan) == 0){
                $mostrar_caducos = null;
            }
            if ($this->request->wantsJson()) {
                if ($this->request->get('all') || $this->request->get('paginate')) {
                    return $this->sendResponse($solicitud, 'SUCCESS');
                } else {
                    if ($filtrarCentro) {

                        $total = Solicitud::where('centro_id', $centro_id)->count();
                    } else {
                        $total = Solicitud::count();
                    }

                    $draw = $this->request->get('draw');
                    return $this->sendResponseDatatable($total, $filtered, $draw, $solicitud, null);
                }
            }
            $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
            $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->orWhere("tipo_archivo_id", 10)->get();
            $tipo_solicitud = array_pluck(TipoSolicitud::all(), 'nombre', 'id');
            $conciliadores = Conciliador::where('centro_id', $centro_id)->with('persona')->get()->pluck('persona.FullName', 'id');
            return view('expediente.solicitudes.index', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes', 'clasificacion_archivos_Representante', 'clasificacion_archivo', 'tipo_solicitud', 'conciliadores','caducan','mostrar_caducos'));
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            if ($this->request->wantsJson()) {
                return $this->sendResponseDatatable(0, 0, 0, [], null);
            }
            $mostrar_caducos = null;
            $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
            $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->orWhere("tipo_archivo_id", 10)->get();
            $tipo_solicitud = array_pluck(TipoSolicitud::all(), 'nombre', 'id');
            $centro_id = Auth::user()->centro_id;
            $conciliadores = Conciliador::where('centro_id', $centro_id)->with('persona')->get()->pluck('persona.FullName', 'id');
            $caducan = 0;
            return view('expediente.solicitudes.index', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes', 'clasificacion_archivos_Representante', 'clasificacion_archivo', 'tipo_solicitud', 'conciliadores','caducan','mostrar_caducos'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $tipo_solicitud_id = isset($this->request->solicitud) ? intval($this->request->solicitud) : 1;
        if ($tipo_solicitud_id > 4) {
            $tipo_solicitud_id = 1;
        }
        if ($tipo_solicitud_id == 1) {
            $tipo_objeto_solicitudes_id = 1;
        } else if ($tipo_solicitud_id == 2) {
            $tipo_objeto_solicitudes_id = 2;
        } else {
            $tipo_objeto_solicitudes_id = 3;
        }
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::where('tipo_objeto_solicitudes_id', $tipo_objeto_solicitudes_id)->get(), 'nombre', 'id');
        $estatus_solicitudes = $this->cacheModel('estatus_solicitudes', EstatusSolicitud::class);
        $tipos_vialidades = $this->cacheModel('tipos_vialidades', TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos', TipoAsentamiento::class);
        $estados = Estado::all(); //$this->cacheModel('estados',Estado::class);
        $jornadas = $this->cacheModel('jornadas', Jornada::class);
        $nacionalidades = $this->cacheModel('nacionalidades', Nacionalidad::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales', GiroComercial::class);
        $ocupaciones = $this->cacheModel('ocupaciones', Ocupacion::class);
        $grupos_prioritarios = $this->cacheModel('grupo_prioritario', GrupoPrioritario::class);
        $lengua_indigena = $this->cacheModel('lengua_indigena', LenguaIndigena::class);
        $generos = $this->cacheModel('generos', Genero::class);
        $tipo_contacto = $this->cacheModel('tipo_contacto', TipoContacto::class);
        $periodicidades = $this->cacheModel('periodicidades', Periodicidad::class);
        $motivo_excepcion = $this->cacheModel('motivo_excepcion', MotivoExcepcion::class);

        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $giros = GiroComercial::where("parent_id", 1)->orderBy('nombre')->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->orWhere("tipo_archivo_id", 10)->get();
        // $municipios = $this->cacheModel('municipios',Municipio::class,'municipio');
        //$municipios = array_pluck(Municipio::all(),'municipio','id');
        $municipios = [];
        return view('expediente.solicitudes.create', compact('objeto_solicitudes', 'estatus_solicitudes', 'tipos_vialidades', 'tipos_asentamientos', 'estados', 'jornadas', 'generos', 'nacionalidades', 'giros_comerciales', 'ocupaciones', 'lengua_indigena', 'tipo_contacto', 'periodicidades', 'municipios', 'grupos_prioritarios', 'motivo_excepcion', 'clasificacion_archivo', 'tipo_solicitud_id', 'clasificacion_archivos_Representante', 'giros'));
    }

    /**
     * Función para almacenar catalogos (nombre,id) en cache
     *
     * @param [string] $nombre
     * @param [Model] $modelo
     * @return void
     */
    private function cacheModel($nombre, $modelo, $campo = 'nombre') {
        if (!Cache::has($nombre)) {
            $respuesta = array_pluck($modelo::all(), $campo, 'id');
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
        if ($solicitud["tipo_solicitud_id"] == 1) {
            $request->validate([
                'objeto_solicitudes' => 'required',
                'solicitud.fecha_conflicto' => 'required|date_format:Y-m-d',
                'solicitud.solicita_excepcion' => 'required',
                'solicitud.tipo_solicitud_id' => 'required',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable'],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|date_format:Y-m-d',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.dato_laboral' => 'required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable'],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]);
        } else {
            $request->validate([
                'objeto_solicitudes' => 'required',
                'solicitud.fecha_conflicto' => 'required|date_format:Y-m-d',
                'solicitud.solicita_excepcion' => 'required',
                'solicitud.tipo_solicitud_id' => 'required',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable'],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|date_format:Y-m-d',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable'],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]);
        }

        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(1, 1);
        DB::beginTransaction();
        try {
            // Solicitud
            $userAuth = Auth::user();
            if ($userAuth) {
                $solicitud['user_id'] = Auth::user()->id;
                $solicitud['captura_user_id'] = Auth::user()->id;
            }
            // Se registra la solicitud con estatus sin ratificar
            $solicitud['estatus_solicitud_id'] = 1;
            // Si no esta seleccionado el tipo de solicitud se pone la 1  - Trabajador individual
            if (!isset($solicitud['tipo_solicitud_id'])) {
                $solicitud['tipo_solicitud_id'] = 1;
            }
            $tipo_solicitud_id = $solicitud['tipo_solicitud_id'];
            $date = new \DateTime();
            $solicitud['fecha_recepcion'] = $date->format('Y-m-d H:i:s');
            $solicitud['centro_id'] = $this->getCentroId();
            //Obtenemos el contador para solicitud, se manda tipo contador (1 solicitud) y centro_id
            $solicitud['folio'] = $folio->contador;
            $solicitud['anio'] = $folio->anio;
            $solicitud['ratificada'] = false;
            // Si es solicitud virtual se asigna canal unico para liga unica
            if ($solicitud['virtual'] == "true") {
                $canal = CanalFolio::inRandomOrder()->first();
                $solicitud['canal'] = $canal->folio;
                $canal->delete();
            }
            $solicitudSaved = Solicitud::create($solicitud);
            $objeto_solicitudes = $request->input('objeto_solicitudes');
            foreach ($objeto_solicitudes as $key => $value) {
                $solicitudSaved->objeto_solicitudes()->attach($value['objeto_solicitud_id']);
            }

            $solicitantes = $request->input('solicitantes');

            $centro = null;
            // Se recorren todos los solicitantes
            foreach ($solicitantes as $key => $solicitante) {

                $solicitante['solicitud_id'] = $solicitudSaved['id'];
                $solicitudFilter = Arr::except($solicitante, ['activo', 'domicilios', 'contactos', 'dato_laboral', 'tmp_files', 'clasificacion_archivo_id']);
                $parteSaved = Parte::create($solicitante);
                //Se agrega el registro de los datos laborales del solicitante
                $dato_laboral = [];
                if (isset($solicitante['dato_laboral'])) {
                    $dato_laboral = $solicitante['dato_laboral'];
                    $parteSaved->dato_laboral()->create($dato_laboral);
                }
                //Si hay archivo temporal se agrega para cada solicitante
                if (isset($solicitante['tmp_files'])) {
                    $clasificacion_archivo_id = $solicitante['clasificacion_archivo_id'];
                    $tmp_files = $solicitante['tmp_files'];
                    unset($solicitante['tmp_files']);
                    unset($solicitante['clasificacion_archivo_id']);
                }
                if (isset($tmp_files)) {
                    foreach ($tmp_files as $index => $tmp_file) {
                        $solicitud_id = $solicitudSaved->id;
                        $clasificacion_archivo = $clasificacion_archivo_id;
                        $directorio = 'solicitud/' . $solicitud_id . '/parte/' . $parteSaved->id;
                        $file_name = basename($tmp_file);
                        $complete_path = $directorio . "/" . $file_name;
                        Storage::makeDirectory($directorio);
                        $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                        Storage::copy($tmp_file, $complete_path);
                        $path = $complete_path;
                        $uuid = Str::uuid();
                        $documento = $parteSaved->documentos()->create([
                            "nombre" => $file_name,
                            "nombre_original" => $file_name,
                            "descripcion" => $tipoArchivo->nombre,
                            "ruta" => $path,
                            "uuid" => $uuid,
                            "tipo_almacen" => "local",
                            "uri" => $path,
                            "longitud" => round(Storage::size($path) / 1024, 2),
                            "firmado" => "false",
                            "clasificacion_archivo_id" => $tipoArchivo->id,
                        ]);
                    }
                }
                //Se agrega el registro de los domicilios del solicitante
                $domicilios = [];
                if ($solicitante["domicilios"] && $solicitante["domicilios"][0]) {
                    $domicilio = $solicitante["domicilios"][0];
                    unset($domicilio['activo']);
                    $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                }
                //Si la solicitud es de tipo 2 (Patron individual) o 3 (Patron colectivo) se selecciona el centro del solicitante
                if ($key == 0 && ($tipo_solicitud_id == 2 || $tipo_solicitud_id == 3 )) {
                    $domiciliop = $domicilio["estado_id"];
                    $centro = $this->getCentroId($domicilio["estado_id"], $domicilio['municipio']);
                    $domicilioCentro = Centro::find($centro)->domicilio;
                    if ($domicilioCentro) {
                        $estadoSelect = Estado::find($domicilioCentro->estado_id);
                        if (!$estadoSelect->en_vigor) {
                            return $this->sendError(' Lamentamos que su estado no esté incluido en la etapa actual de la implementación de la reforma a la justicia laboral ', 'Error');
                        }
                    } else {
                        return $this->sendError(' Lamentamos que su estado no esté incluido en la etapa actual de la implementación de la reforma a la justicia laboral ', 'Error');
                    }
                }
                //Se agrega el registro de los contactos del solicitante
                $contactos = [];
                if (isset($solicitante["contactos"])) {
                    $contactos = $solicitante["contactos"];
                    if (count($contactos) > 0) {
                        foreach ($contactos as $key2 => $contacto) {
                            unset($contacto['activo']);
                            $contactoSaved = $parteSaved->contactos()->create($contacto);
                        }
                    }
                }
            }

            $solicitados = $request->input('solicitados');
            // Se recorren todos los citados
            foreach ($solicitados as $key => $solicitado) {
                $solicitado['solicitud_id'] = $solicitudSaved['id'];
                $solicitudFilter = Arr::except($solicitado, ['activo', 'domicilios', 'contactos', 'dato_laboral']);
                $parteSaved = Parte::create($solicitudFilter);
                //Se agrega el registro de los datos laborales del citado
                $dato_laboral = [];
                if (isset($solicitado['dato_laboral'])) {
                    $dato_laboral = $solicitado['dato_laboral'];
                    $parteSaved->dato_laboral()->create($dato_laboral);
                }
                //Se agrega el registro de los domicilios del citado
                $domicilios = [];
                if (isset($solicitado["domicilios"])) {
                    $domicilios = $solicitado["domicilios"];
                    //Si la solicitud es de tipo 1 (Trabajador individual) o 4 (Sindical) se selecciona el centro del solicitante
                    if ($key == 0 && ($tipo_solicitud_id == 1 || $tipo_solicitud_id == 4 )) {
                        $domiciliop = $domicilios[0]["estado_id"];
                        $centro = $this->getCentroId($domicilios[0]["estado_id"], $domicilios[0]['municipio']);
                        $domicilioCentro = Centro::find($centro)->domicilio;
                        if ($domicilioCentro) {
                            $estadoSelect = Estado::find($domicilioCentro->estado_id);
                            if (!$estadoSelect->en_vigor) {
                                return $this->sendError(' Lamentamos que su estado no esté incluido en la etapa actual de la implementación de la reforma a la justicia laboral ', 'Error');
                            }
                        } else {
                            return $this->sendError(' Lamentamos que su estado no esté incluido en la etapa actual de la implementación de la reforma a la justicia laboral ', 'Error');
                        }
                    }
                    if (count($domicilios) > 0) {
                        foreach ($domicilios as $domicilio) {
                            $domicilio = Arr::except($domicilio, ['activo']);
                            $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                        }
                    }
                }
                //Se agrega el registro de los contactos del citado
                $contactos = [];
                if (isset($solicitado["contactos"])) {
                    $contactos = $solicitado["contactos"];
                    if (count($contactos) > 0) {
                        foreach ($contactos as $contacto) {
                            $contacto = Arr::except($contacto, ['activo']);
                            $contactoSaved = $parteSaved->contactos()->create($contacto);
                        }
                    }
                }
            }
            if ($centro != null) {
                $solicitudSaved->update(["centro_id" => $centro]);
            } else {
                DB::rollback();
                return $this->sendError(' Lamentamos que su municipio no está incluido en la etapa actual de la implementación de la reforma a la justicia laboral ', 'Error');
            }
            // // Para cada objeto obtenido cargamos sus relaciones.
            $solicitudSaved = tap($solicitudSaved)->each(function ($solicitudSaved) {
                $solicitudSaved->loadDataFromRequest();
            });
            DB::commit();
            // generar acuse de solicitud
            event(new GenerateDocumentResolution("", $solicitudSaved->id, 40, 6));
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al crear la solicitud', 'Error');
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
    private function getCentroId($estado_id = null, $municipio = null) {
        if ($estado_id != null) {
            $centro = Centro::find($estado_id);
            if ($centro && $centro->sedes_multiples) {
                $centro_municipio = CentroMunicipio::where("municipio", $municipio)->first();
                if ($centro_municipio != null) {
                    $centro = Centro::find($centro_municipio->centro_id);
                }
                Log::debug("El estado asignado tiene multiples sedes, el municipio asignado es" . $municipio . ",  se busca el centro que respalda ese municipio, se encuentra el siguiente: " . print_r($centro_municipio, true) . " Se asigno el centro" . print_r($centro, true));
            }
        } else {
            $centro = Centro::inRandomOrder()->first();
        }
        return $centro->id;
    }

    /**
     * Función para guardar modificar y eliminar disponibilidades
     * @param Request $request
     * @return Centro $centro
     */
    public function getSedeMultiple(Request $request) {
        $centro = Centro::find($request->estado_id);

        if ($centro != null && $centro->sedes_multiples) {
            $municipios = CentroMunicipio::all('municipio')->toArray();
            return $this->sendResponse($municipios, 'SUCCESS');
        }
        return $this->sendResponse([], 'SUCCESS');
    }

    /**
     * Display the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $solicitud = Solicitud::with('expediente', 'giroComercial', 'estatusSolicitud', 'centro', 'tipoIncidenciaSolicitud', 'giroComercial.ambito', 'objeto_solicitudes')->find($id);
        $partes = $solicitud->partes()->with('dato_laboral', 'domicilios', 'contactos', 'lenguaIndigena')->get();

        $solicitantes = $partes->where('tipo_parte_id', 1);

        foreach ($solicitantes as $key => $value) {
            // $value->dato_laboral;
            // $value->domicilios;
            // $value->contactos;
            // $value->lenguaIndigena;
            $solicitantes[$key]["activo"] = 1;
        }
        $solicitados = $partes->where('tipo_parte_id', 2);
        foreach ($solicitados as $key => $value) {
            // $value->dato_laboral;
            // $value->domicilios;
            // $value->contactos;
            $solicitados[$key]["activo"] = 1;
        }
        $solicitud["solicitados"] = $solicitados;
        $solicitud["solicitantes"] = $solicitantes;
        //$solicitud->objeto_solicitudes;
        // $solicitud->expediente = $solicitud->expediente;
        // $solicitud->giroComercial = $solicitud->giroComercial;
        // $solicitud->estatusSolicitud = $solicitud->estatusSolicitud;
        // $solicitud->centro = $solicitud->centro;
        // $solicitud->tipoIncidenciaSolicitud = $solicitud->tipoIncidenciaSolicitud;
        // if($solicitud->giroComercial){
        //     $solicitud->giroComercial->ambito;
        // }
        return $solicitud;
    }

    /**
     * Display the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function getSolicitudByFolio(Request $request) {
        try {
            $user = auth()->user();
            $rolActual = $request->session()->get('rolActual')->name;
            $centro = $user->centro;
            $doc = collect();
            $solicitud = Solicitud::with('expediente', 'giroComercial', 'estatusSolicitud', 'centro', 'tipoIncidenciaSolicitud', 'tipoSolicitud', 'giroComercial.ambito', 'objeto_solicitudes')->where('folio', $request->folio)->where('anio', $request->anio);
            if ($rolActual != 'Super Usuario') {
                $centro_id = Auth::user()->centro_id;
                $solicitud->where('centro_id', $centro_id);
            } 
            $solicitud = $solicitud->first();
            $validate = $request->validate;
            if($validate){
                if($rolActual == 'Personal conciliador'){
                    if ($solicitud->expediente) {
                        $audiencias = $solicitud->expediente->audiencia()->orderBy('id', 'desc')->first();
                        if($audiencias){
                            $conciliadorAudiencia = ConciliadorAudiencia::where('audiencia_id',$audiencias->id)->first();
                            $persona_id = Auth::user()->persona->id;
                            $conciliador = Conciliador::where('persona_id',$persona_id)->first();
                            if($conciliador && $conciliadorAudiencia && $conciliador->id != $conciliadorAudiencia->conciliador_id ){
                                return response()->json(['success' => false, 'message' => 'No tienes permisos para acceder a esta solicitud', 'data' => null], 200);
                            }
                        }
                    }else{
                        return response()->json(['success' => false, 'message' => 'No tienes permisos para acceder a esta solicitud', 'data' => null], 200);
                    }
                }
            }
            if ($solicitud) {
                $documentos = $solicitud->documentos;
                foreach ($documentos as $documento) {
                    if ($documento->ruta != "") {
                        $documento->id = $documento->id;
                        $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                        $documento->tipo = pathinfo($documento->ruta)['extension'];
                        $documento->uuid = $documento->uuid;
                        $documento->owner = "Solicitud ".$solicitud->folio."/".$solicitud->anio;
                        $doc->push($documento);
                    }
                }
                $partes = $solicitud->partes()->with('dato_laboral', 'domicilios', 'contactos', 'lenguaIndigena')->get();
                foreach($partes as $parte){
                    $documentos = $parte->documentos;
                    foreach ($documentos as $documento) {
                        $documento->id = $documento->id;
                        $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                        $documento->tipo = pathinfo($documento->ruta)['extension'];
                        if ($parte->tipo_persona_id == 1) {
                            $documento->owner = $parte->nombre . " " . $parte->primer_apellido . " " . $parte->segundo_apellido;
                        } else {
                            $documento->owner = $parte->nombre_comercial;
                        }
                        $documento->tipo_doc = 3;
                        $doc->push($documento);
                    }
                }
                $solicitantes = $partes->where('tipo_parte_id', 1);

                foreach ($solicitantes as $key => $value) {
                    $solicitantes[$key]["activo"] = 1;
                }
                $solicitados = $partes->where('tipo_parte_id', 2);
                foreach ($solicitados as $key => $value) {
                    $solicitados[$key]["activo"] = 1;
                }
                $solicitud["solicitados"] = $solicitados;
                $solicitud["solicitantes"] = $solicitantes;
                if ($solicitud->expediente) {
                    $solicitud->audiencias = $solicitud->expediente->audiencia()->orderBy('id', 'asc')->get();
                    foreach ($solicitud->audiencias as $audiencia) {
                        $documentos = $audiencia->documentos;
                        foreach ($documentos as $documento) {
                            $documento->id = $documento->id;
                            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                            $documento->tipo = pathinfo($documento->ruta)['extension'];
                            $documento->tipo_doc = 3;
                            $documento->owner = "Audiencia ".$audiencia->folio . "/" . $audiencia->anio;
                            $documento->audiencia_id = $audiencia->id;
                            $doc->push($documento);
                        }
                        if ($audiencia->conciliador) {
                            $audiencia->conciliador->persona;
                        }
                        $audiencia->iniciada = false;
                        if (count($audiencia->comparecientes) > 0) {
                            $audiencia->iniciada = true;
                        }
                        foreach ($audiencia->audienciaParte as $parte) {
                            $documentos = $parte->documentos;
                            foreach ($documentos as $documento) {
                                $documento->id = $documento->id;
                                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                                $documento->tipo = pathinfo($documento->ruta, PATHINFO_EXTENSION);
                                $documento->owner = "Audiencia ".$audiencia->folio . "/" . $audiencia->anio;
                                if ($parte->parte->tipo_persona_id == 1) {
                                    $documento->audiencia = $parte->parte->nombre . " " . $parte->parte->primer_apellido . " " . $parte->parte->segundo_apellido;
                                } else {
                                    $documento->audiencia = $parte->parte->nombre_comercial;
                                }
                                $documento->tipo_doc = 3;
                                $doc->push($documento);
                            }
                        }
                    }
                }
                $solicitud->docs = $doc;
                return response()->json(['success' => true, 'message' => 'Se genero el documento correctamente', 'data' => $solicitud], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontraron datos relacionados', 'data' => null], 200);
            }
        } catch (Exception $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'No se encontraron datos relacionados', 'data' => null], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $doc = [];
        $solicitud = Solicitud::find($id);
        $expediente = Expediente::where("solicitud_id", "=", $solicitud->id)->get();
        if (count($expediente) > 0) {
            $audiencias = Audiencia::where("expediente_id", "=", $expediente[0]->id)->orderBy('id', 'desc')->get();

            $conciliadorAudiencia = ConciliadorAudiencia::where('audiencia_id',$audiencias->first()->id)->first();
            $persona_id = Auth::user()->persona->id;
            $conciliador = Conciliador::where('persona_id',$persona_id)->first();
            if($conciliador && $conciliadorAudiencia && $conciliador->id != $conciliadorAudiencia->conciliador_id ){
                return redirect('solicitudes')->withError('No tienes permisos para acceder a esta solicitud');
            }
        } else {
            $audiencias = array();
        }
        $partes = array();
        foreach ($solicitud->partes as $key => $parte) {
            $parte->tipoParte = $parte->tipoParte;
            $parte->domicilios = $parte->domicilios()->first();
            $partes[$key] = $parte;
        }

        $tipo_solicitud_id = isset($solicitud->tipo_solicitud_id) ? $solicitud->tipo_solicitud_id : 1;
        if ($tipo_solicitud_id == 1) {
            $tipo_objeto_solicitudes_id = 1;
        } else if ($tipo_solicitud_id == 2) {
            $tipo_objeto_solicitudes_id = 2;
        } else {
            $tipo_objeto_solicitudes_id = 3;
        }
        $objeto_solicitudes = array_pluck(ObjetoSolicitud::where('tipo_objeto_solicitudes_id', $tipo_objeto_solicitudes_id)->get(), 'nombre', 'id');
        $estatus_solicitudes = $this->cacheModel('estatus_solicitudes', EstatusSolicitud::class);
        $giros_comerciales = $this->cacheModel('giros_comerciales', GiroComercial::class);
        $tipos_vialidades = $this->cacheModel('tipos_vialidades', TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos', TipoAsentamiento::class);
        $estados = Estado::all(); //$this->cacheModel('estados',Estado::class);
        $jornadas = $this->cacheModel('jornadas', Jornada::class);
        $generos = $this->cacheModel('generos', Genero::class);
        $nacionalidades = $this->cacheModel('nacionalidades', Nacionalidad::class);
        $ocupaciones = $this->cacheModel('ocupaciones', Ocupacion::class);
        $grupo_prioritario = $this->cacheModel('grupo_prioritario', GrupoPrioritario::class);
        $lengua_indigena = $this->cacheModel('lengua_indigena', LenguaIndigena::class);
        $tipo_contacto = $this->cacheModel('tipo_contacto', TipoContacto::class);
        $periodicidades = $this->cacheModel('periodicidades', Periodicidad::class);
        $audits = $this->getAcciones($solicitud, $solicitud->partes, $audiencias, $expediente);
        $municipios = array_pluck(Municipio::all(), 'municipio', 'id');
        $motivo_excepciones = $this->cacheModel('motivo_excepcion', MotivoExcepcion::class);
        $clasificacion_archivo = ClasificacionArchivo::where("tipo_archivo_id", 1)->get();
        $clasificacion_archivos_Representante = ClasificacionArchivo::where("tipo_archivo_id", 9)->orWhere("tipo_archivo_id", 10)->get();

        $conciliadores = array_pluck(Conciliador::with('persona')->get(), "persona.nombre", 'id');
        $giros = GiroComercial::where("parent_id", 1)->orderBy('nombre')->get();
        // consulta de documentos
        return view('expediente.solicitudes.edit', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes', 'tipos_vialidades', 'tipos_asentamientos', 'estados', 'jornadas', 'generos', 'nacionalidades', 'giros_comerciales', 'ocupaciones', 'expediente', 'audiencias', 'grupo_prioritario', 'lengua_indigena', 'tipo_contacto', 'periodicidades', 'audits', 'municipios', 'partes', 'motivo_excepciones', 'conciliadores', 'clasificacion_archivo', 'tipo_solicitud_id', 'clasificacion_archivos_Representante', 'giros'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function consulta($id) {
        try {
            $doc = collect();
            $solicitud = Solicitud::with('expediente', 'giroComercial', 'estatusSolicitud', 'centro', 'tipoIncidenciaSolicitud', 'giroComercial.ambito', 'objeto_solicitudes')->find($id);
            $partes = $solicitud->partes()->with('dato_laboral', 'domicilios', 'contactos', 'lenguaIndigena')->get();
            //Consulta de solicitud con relaciones

            $solicitantes = $partes->where('tipo_parte_id', 1);

            foreach ($solicitantes as $key => $value) {
                $solicitantes[$key]["activo"] = 1;
            }
            $solicitados = $partes->where('tipo_parte_id', 2);
            foreach ($solicitados as $key => $value) {
                $solicitados[$key]["activo"] = 1;
            }
            $solicitud["solicitados"] = $solicitados;
            $solicitud["solicitantes"] = $solicitantes;
            $estatus_solicitud_id = $solicitud->estatus_solicitud_id;
            //Consulta de solicitud con relaciones

            $expediente_id = '';
            $expediente = Expediente::where("solicitud_id", "=", $solicitud->id)->get();
            if (count($expediente) > 0) {
                $expediente_id = $expediente[0]->id;
                $audiencias = Audiencia::where("expediente_id", "=", $expediente[0]->id)->withCount('etapasResolucionAudiencia')->orderBy('id', 'asc')->get();
                foreach ($audiencias as $audiencia) {
                    foreach ($audiencia->audienciaParte as $parte) {
                        $documentos = $parte->documentos;
                        foreach ($documentos as $documento) {
                            $documento->id = $documento->id;
                            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                            $documento->tipo = pathinfo($documento->ruta, PATHINFO_EXTENSION);
                            if ($parte->parte->tipo_persona_id == 1) {
                                $documento->audiencia = $parte->parte->nombre . " " . $parte->parte->primer_apellido . " " . $parte->parte->segundo_apellido;
                            } else {
                                $documento->audiencia = $parte->parte->nombre_comercial;
                            }
                            $documento->tipo_doc = 3;
                            $doc->push($documento);
                        }
                    }
                }
            } else {
                $audiencias = array();
            }
            $partes = array();
            foreach ($solicitud->partes as $key => $parte) {
                $parte->tipoParte = $parte->tipoParte;
                $parte->domicilios = $parte->domicilios()->first();
                //            dd($parte);
                $partes[$key] = $parte;
                $documentos = $parte->documentos;
                foreach ($documentos as $documento) {
                    $documento->id = $documento->id;
                    $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                    $documento->tipo = pathinfo($documento->ruta, PATHINFO_EXTENSION);
                    $documento->parte = $parte->nombre . " " . $parte->primer_apellido . " " . $parte->segundo_apellido;
                    $documento->tipo_doc = 2;
                    $doc->push($documento);
                }
            }

            $tipo_solicitud_id = isset($solicitud->tipo_solicitud_id) ? $solicitud->tipo_solicitud_id : 1;
            if ($tipo_solicitud_id == 1) {
                $tipo_objeto_solicitudes_id = 1;
            } else if ($tipo_solicitud_id == 2) {
                $tipo_objeto_solicitudes_id = 2;
            } else {
                $tipo_objeto_solicitudes_id = 3;
            }

            // dd(Conciliador::all()->persona->full_name());
            $conciliadores = array_pluck(Conciliador::with('persona')->get(), "persona.nombre", 'id');
            // dd($conciliador);
            // $conciliadores = $this->cacheModel('conciliadores',Conciliador::class);
            // consulta de documentos


            $documentos = $solicitud->documentos;
            foreach ($documentos as $documento) {
                $documento->id = $documento->id;
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta, PATHINFO_EXTENSION);
                $documento->tipo_doc = 1;
                $doc->push($documento);
            }
            if ($solicitud->expediente && $solicitud->expediente->audiencia) {
                foreach ($solicitud->expediente->audiencia as $audiencia) {
                    $documentos = $audiencia->documentos;
                    foreach ($documentos as $documento) {
                        $documento->id = $documento->id;
                        $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                        $documento->tipo = pathinfo($documento->ruta, PATHINFO_EXTENSION);
                        $documento->tipo_doc = 3;
                        $documento->audiencia = $audiencia->folio . "/" . $audiencia->anio;
                        $documento->audiencia_id = $audiencia->id;
                        $doc->push($documento);
                    }
                }
            }

            $documentos = $doc->sortBy('id');
            //termina consulta de documentos
            return view('expediente.solicitudes.consultar', compact('solicitud', 'audiencias', 'documentos', 'estatus_solicitud_id', 'expediente_id'));
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return redirect('solicitudes')->withError('Hay un error en la solicitud no es posible acceder');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitud $solicitud) {
        if ($solicitud["tipo_solicitud_id"] == 1) {
            $request->validate([
                'objeto_solicitudes' => 'required',
                'solicitud.fecha_conflicto' => 'required|date_format:m/d/Y',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable'],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|date_format:Y-m-d',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.dato_laboral' => 'required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable'],
                'solicitados.*.tipo_parte_id' => 'required',
                'solicitados.*.tipo_persona_id' => 'required',
                'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
                'solicitados.*.domicilios' => 'required'
            ]);
        } else {
            $request->validate([
                'objeto_solicitudes' => 'required',
                'solicitud.fecha_conflicto' => 'required|date_format:Y-m-d',
                'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.rfc' => ['nullable'],
                'solicitantes.*.tipo_parte_id' => 'required',
                'solicitantes.*.tipo_persona_id' => 'required',
                'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
                'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
                'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|date_format:Y-m-d',
                'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
                'solicitantes.*.domicilios' => 'required',
                'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
                'solicitados.*.rfc' => ['nullable'],
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
            // Se recorren todos los solicitantes
            foreach ($solicitantes as $key => $solicitante) {
                $solicitante['solicitud_id'] = $solicitudSaved['id'];
                if ($solicitante['activo'] == "1") {
                    unset($solicitante['activo']);
                    $domicilio = $solicitante["domicilios"][0];
                    // Se revisa si la parte no tiene un id para crearla
                    if (!isset($solicitante["id"]) || $solicitante["id"] == "") {
                        $solicitanteSave = Arr::except($solicitante, ['activo', 'domicilios', 'contactos', 'dato_laboral', 'clasificacion_archivo_id']);
                        $parteSaved = Parte::create($solicitanteSave);
                        // Si tiene se registra dato laboral
                        if (isset($solicitante['dato_laboral'])) {
                            $dato_laboral = $solicitante['dato_laboral'];
                            $parteSaved = ($parteSaved->dato_laboral()->create($dato_laboral)->parte);
                        }
                        unset($domicilio['activo']);
                        // Si tiene se registra domicilio
                        $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                        if (isset($solicitante["contactos"])) {
                            $contactos = $solicitante["contactos"];
                            if (count($contactos) > 0) {
                                foreach ($contactos as $key => $contacto) {
                                    unset($contacto['activo']);
                                    $contactoSaved = $parteSaved->contactos()->create($contacto);
                                }
                            }
                        }
                    } else {
                        // Si la parte ya existe solo se actualiza la información
                        $parteSaved = Parte::find($solicitante['id']);
                        $solicitanteUpd = Arr::except($solicitante, ['activo', 'domicilios', 'contactos', 'dato_laboral', 'clasificacion_archivo_id']);
                        $parteUpdated = $parteSaved->update($solicitanteUpd);
                        $parteSaved = Parte::find($solicitante['id']);
                        // Se valida si existen datos laborales si no se registra uno nuevo
                        if (isset($solicitante['dato_laboral'])) {
                            $dato_laboral = $solicitante['dato_laboral'];
                            if (isset($dato_laboral["id"]) && $dato_laboral["id"] != "") {
                                $dato_laboralUp = DatoLaboral::find($dato_laboral["id"]);
                                $dato_laboralUp->update($dato_laboral);
                            } else {
                                $dato_laboral = ($parteSaved->dato_laboral()->create($dato_laboral));
                            }
                        }
                        unset($domicilio['activo']);
                        // Se valida si se existe domicilio si no se agrega
                        if (isset($domicilio["id"]) && $domicilio["id"] != "") {
                            $domicilioUp = Domicilio::find($domicilio["id"]);
                            $domicilioUp->update($domicilio);
                        } else {
                            $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                        }
                        // Se valida si se existen contactos si no se agregan
                        if (isset($solicitante["contactos"])) {
                            $contactos = $solicitante["contactos"];
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
                    }
                } else {
                    // Si la variable activo esta en 0 se elimina la parte
                    $parteSaved = Parte::find($solicitante['id']);
                    $parteSaved = $parteSaved->delete();
                }
            }

            $solicitados = $request->input('solicitados');
            // Se recorren todos los citados
            foreach ($solicitados as $key => $citado) {
                if ($citado['activo'] == "1") {

                    $domicilios = Array();
                    $contactos = Array();
                    $citado['solicitud_id'] = $solicitudSaved['id'];
                    if (!isset($citado["id"]) || $citado["id"] == "") {
                        $citadoSave = Arr::except($citado, ['activo', 'domicilios', 'contactos', 'dato_laboral', 'clasificacion_archivo_id']);
                        $parteSaved = Parte::create($citadoSave);
                        // Se valida si se existen datos laborales si no se agregan
                        if (isset($citado['dato_laboral'])) {
                            $dato_laboral = $citado['dato_laboral'];
                            $parteSaved = ($parteSaved->dato_laboral()->create($dato_laboral)->parte);
                        }
                        // Se valida si se existen domicilios si no se agregan
                        if (isset($citado["domicilios"])) {
                            $domicilios = $citado["domicilios"];
                            if (count($domicilios) > 0) {
                                foreach ($domicilios as $key => $domicilio) {
                                    unset($domicilio['activo']);
                                    $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                                }
                            }
                        }
                        // Se valida si se existen contactos si no se agregan
                        if (isset($citado["contactos"])) {
                            $contactos = $citado["contactos"];
                            foreach ($contactos as $key => $contacto) {
                                unset($contacto['activo']);
                                $contactoSaved = $parteSaved->contactos()->create($contacto);
                            }
                        }
                        // Si ya hay audiencias registradas se busca la ultima y se agrega el citado a al audiencia y se envian todos los citatorios
                        if ($solicitudSaved->ratificada && $solicitudSaved->expediente) {
                            $audiencias = $solicitudSaved->expediente->audiencia()->orderBy('id', 'desc');
                            if (!empty($audiencias)) {
                                $audiencia = $audiencias->first();
                                if (!$audiencia->finalizada) {
                                    AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parteSaved->id, "tipo_notificacion_id" => 3]);
                                    event(new GenerateDocumentResolution($audiencia->id, $solicitudSaved->id, 14, 4, null, $parteSaved->id));
                                }
                            }
                        }
                    } else {
                        $parteSaved = Parte::find($citado['id']);
                        $citadoSave = Arr::except($citado, ['activo', 'domicilios', 'contactos', 'dato_laboral', 'clasificacion_archivo_id']);
                        $parteSaved->update($citadoSave);
                        // Se valida si se existen datos laborales si no se agregan
                        if (isset($citado['dato_laboral'])) {
                            $dato_laboral = $citado['dato_laboral'];
                            if (isset($dato_laboral["id"]) && $dato_laboral["id"] != "") {
                                $dato_laboralUp = DatoLaboral::find($dato_laboral["id"]);
                                $dato_laboralUp->update($dato_laboral);
                            } else {
                                $dato_laboral = $parteSaved->dato_laboral()->create($dato_laboral);
                            }
                        }
                        // Se valida si se existen domicilios si no se agregan
                        if (isset($citado["domicilios"])) {
                            $domicilios = $citado["domicilios"];
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
                        }
                        // Se valida si se existen contactos si no se agregan
                        if (isset($citado["contactos"])) {
                            $contactos = $citado["contactos"];
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
                    }
                } else {
                    // Si la variable activo esta en 0 se elimina la parte
                    $parteSaved = Parte::find($citado['id']);
                    //event(new GenerateDocumentResolution(null,$solicitudSaved->id,59,21,null,$parteSaved->id));
                    $parteSaved->delete();
                }
            }
            // // Para cada objeto obtenido cargamos sus relaciones.
            $solicitudSaved = tap($solicitudSaved)->each(function ($solicitudSaved) {
                $solicitudSaved->loadDataFromRequest();
            });
            DB::commit();
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error' . $e->getMessage());
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
        try {
            $solicitud = Solicitud::find($request->id);
            $ContadorController = new ContadorController();
            //Obtenemos el contador
            $folioC = $ContadorController->getContador(1, $solicitud->centro->id);
            $edo_folio = $solicitud->centro->abreviatura;
            $folio = $edo_folio . "/CJ/I/" . $folioC->anio . "/" . sprintf("%06d", $folioC->contador);
            //Creamos el expediente de la solicitud
            $expediente = Expediente::create(["solicitud_id" => $request->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
            //ratificacion de las partes
            foreach ($solicitud->partes as $key => $parte) {
                if (count($parte->documentos) == 0) {
                    $parte->ratifico = true;
                    $parte->update();
                }
            }
            $user_id = Auth::user()->id;
            $solicitud->update(["estatus_solicitud_id" => 3, "ratificada" => true,"url_virtual" => null, "incidencia" => true,"fecha_incidencia"=>now(),"justificacion_incidencia"=>$this->request->justificacion,"tipo_incidencia_solicitud_id"=>4, "fecha_ratificacion" => now(),"inmediata" => false,'user_id'=>$user_id]);

            // Obtenemos la sala virtual
            $sala = Sala::where("centro_id", $solicitud->centro_id)->where("virtual", true)->first();
            if ($sala == null) {
                DB::rollBack();
                return $this->sendError('No hay salas virtuales disponibles', 'Error');
            }
            $sala_id = $sala->id;
            //obtenemos al conciliador disponible
            $conciliadores = Conciliador::where("centro_id", $solicitud->centro_id)->get();
            $conciliadoresDisponibles = array();
            foreach ($conciliadores as $conciliador) {
                $conciliadorDisponible = false;
                foreach ($conciliador->rolesConciliador as $roles) {
                    if ($roles->rol_atencion_id == 2) {
                        $conciliadorDisponible = true;
                    }
                }
                if ($conciliadorDisponible) {
                    $conciliadoresDisponibles[] = $conciliador;
                }
            }
            $conciliador_id = null;
            if (count($conciliadoresDisponibles) > 0) {
                $conciliador = Arr::random($conciliadoresDisponibles);
            } else {
                DB::rollBack();
                return $this->sendError('No hay conciliadores con rol de previo acuerdo', 'Error');
            }
            $partes = $solicitud->partes;
            foreach ($partes as $parte) {
                if ($parte->tipo_parte_id == 1) {
                    //generar constancia de incompetencia por solicitante
                    event(new GenerateDocumentResolution(null, $solicitud->id, 13, 10, $parte->id, null));
                }
            }
            DB::commit();
            return $solicitud;
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            // dd($e);
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al confirmar la solicitud', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al confirmar la solicitud');
        }
    }

    public function Ratificar(Request $request) {
        if (!self::validarCentroAsignacion()) {
            return $this->sendError('No se ha configurado el centro', 'Error');
            exit;
        }
        $ContadorController = new ContadorController();
        $folioC = $ContadorController->getContador(1,auth()->user()->centro_id);
        $folioAudiencia = $ContadorController->getContador(3, auth()->user()->centro_id);
        DB::beginTransaction();
        $solicitud = Solicitud::find($request->id);
        try {
//            Validamos si ya hay un expediente
            if ($solicitud->expediente == null) {

                //Obtenemos el contador
                $edo_folio = $solicitud->centro->abreviatura;
                $folio = $edo_folio . "/CJ/I/" . $folioC->anio . "/" . sprintf("%06d", $folioC->contador);
                //Creamos el expediente de la solicitud
                $expediente = Expediente::create(["solicitud_id" => $request->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
                foreach ($solicitud->partes as $key => $parte) {
                    if (count($parte->documentos) == 0) {
                        $parte->ratifico = true;
                        $parte->update();
                    }
                }
                $tipo_notificacion_id = null;
                if ($request->inmediata == "true") {
                    $user_id = Auth::user()->id;
                    $solicitud->update(["estatus_solicitud_id" => 2, "url_virtual" => null, "ratificada" => true, "fecha_ratificacion" => now(), "inmediata" => true, 'user_id' => $user_id]);
                    // Obtenemos la sala virtual
                    $sala = Sala::where("centro_id", $solicitud->centro_id)->where("virtual", true)->first();
                    if ($sala == null) {
                        DB::rollBack();
                        return $this->ls
                                        ('No hay salas virtuales disponibles', 'Error');
                    }
                    $sala_id = $sala->id;
                    //                Validamos que el que ratifica sea conciliador
                    if (!auth()->user()->hasRole('Personal conciliador')) {
                        DB::rollBack();
                        return $this->sendError('La solicitud con convenio solo puede ser confirmada por personal conciliador', 'Error');
                    } else {
                        //Buscamos el conciliador del usuario
                        if (isset(auth()->user()->persona->conciliador)) {
                            $conciliador = auth()->user()->persona->conciliador;
                        } else {
                            DB::rollBack();
                            return $this->sendError('El usuario no esta dado de alta en la lista de conciliadores', 'Error');
                        }
                    }

                    //obtenemos al conciliador disponible
                    //                $conciliadores = Conciliador::where("centro_id",$solicitud->centro_id)->get();
                    //                $conciliadoresDisponibles = array();
                    //                foreach($conciliadores as $conciliador){
                    //                    $conciliadorDisponible = false;
                    //                    foreach($conciliador->rolesConciliador as $roles){
                    //                        if($roles->rol_atencion_id == 2){
                    //                            $conciliadorDisponible = true;
                    //                        }
                    //                    }
                    //                    if($conciliadorDisponible){
                    //                        $conciliadoresDisponibles[]=$conciliador;
                    //                    }
                    //                }
                    //                $conciliador_id = null;
                    //                if(count($conciliadoresDisponibles) > 0){
                    //                    $conciliador = Arr::random($conciliadoresDisponibles);
                    //                }else{
                    //                    DB::rollBack();
                    //                    return $this->sendError('No hay conciliadores con rol de previo acuerdo', 'Error');
                    //                }
                    // Registramos la audiencia
                    //Obtenemos el contador
                    //creamos el registro de la audiencia
                    if ($request->fecha_cita == "" || $request->fecha_cita == null) {
                        $fecha_cita = null;
                    } else {
                        $fechaC = explode("/", $request->fecha_cita);
                        $fecha_cita = $fechaC["2"] . "-" . $fechaC["1"] . "-" . $fechaC["0"];
                    }
                    $audiencia = Audiencia::create([
                                "expediente_id" => $expediente->id,
                                "multiple" => false,
                                "fecha_audiencia" => now()->format('Y-m-d'),
                                "hora_inicio" => now()->format('H:i:s'),
                                "hora_fin" => \Carbon\Carbon::now()->addHours(1)->addMinutes(30)->format('H:i:s'),
                                "conciliador_id" => $conciliador->id,
                                "numero_audiencia" => 1,
                                "reprogramada" => false,
                                "anio" => $folioAudiencia->anio,
                                "folio" => $folioAudiencia->contador,
                                "fecha_cita" => $fecha_cita
                    ]);

                    // guardamos la sala y el conciliador a la audiencia
                    ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $conciliador->id, "solicitante" => true]);
                    SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $sala_id, "solicitante" => true]);
                    // Guardamos todas las Partes en la audiencia
                    $partes = $solicitud->partes;
                    foreach ($partes as $parte) {
                        AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parte->id, "tipo_notificacion_id" => null]);
                        if ($parte->tipo_parte_id == 2) {
                            // generar citatorio de conciliacion
                            event(new GenerateDocumentResolution($audiencia->id, $solicitud->id, 14, 4, null, $parte->id));
                        }
                    }
                    $audiencia->tipo_solicitud_id = $audiencia->expediente->solicitud->tipo_solicitud_id;
                    DB::commit();
                    return $audiencia;
                } else {
                    if ((int) $request->tipo_notificacion_id == 1) {
                        $diasHabilesMin = 7;
                        $diasHabilesMax = 10;
                    } else {
                        $diasHabilesMin = 15;
                        $diasHabilesMax = 18;
                    }
                    //                obtenemos el domicilio del centro
                    $domicilio_centro = auth()->user()->centro->domicilio;
                    //                obtenemos el domicilio del citado
                    $partes = $solicitud->partes;
                    $domicilio_citado = null;
                    foreach ($partes as $parte) {
                        if ($parte->tipo_parte_id == 2) {
                            $domicilio_citado = $parte->domicilios()->first();
                            break;
                        }
                    }
                    $user_id = Auth::user()->id;
                    $solicitud->update(["estatus_solicitud_id" => 2, "url_virtual" => null, "ratificada" => true, "fecha_ratificacion" => now(), "inmediata" => false, 'user_id' => $user_id]);
                    $centroResponsable = auth()->user()->centro;
                    if ($solicitud->tipo_solicitud_id == 3 || $solicitud->tipo_solicitud_id == 4) {
                        $centroResponsable = Centro::where("abreviatura", "OCCFCRL")->first();
                    }
                    if ($request->separados == "true") {
                        $datos_audiencia = FechaAudienciaService::obtenerFechaAudienciaDoble(date("Y-m-d"), $centroResponsable, $diasHabilesMin, $diasHabilesMax, $solicitud->virtual);
                        $multiple = true;
                    } else {
                        $datos_audiencia = FechaAudienciaService::obtenerFechaAudiencia(date("Y-m-d"), $centroResponsable, $diasHabilesMin, $diasHabilesMax, $solicitud->virtual);
                        $multiple = false;
                    }
                    //                Solicitamos la fecha limite de notificacion solo cuando el tipo de notificación es por notificador sin cita
                    $fecha_notificacion = null;
                    if ((int) $request->tipo_notificacion_id == 2) {
                        $fecha_notificacion = self::obtenerFechaLimiteNotificacion($domicilio_centro, $domicilio_citado, $datos_audiencia["fecha_audiencia"]);
                    }

                    //Obtenemos el contador
                    //creamos el registro de la audiencia
                    if ($request->fecha_cita == "" || $request->fecha_cita == null) {
                        $fecha_cita = null;
                    } else {
                        $fechaC = explode("/", $request->fecha_cita);
                        $fecha_cita = $fechaC["2"] . "-" . $fechaC["1"] . "-" . $fechaC["0"];
                    }
                    //Agregamos el la etapa de notificación
                    $etapa = \App\EtapaNotificacion::where("etapa", "ilike", "%Ratificación%")->first();

                    $audiencia = Audiencia::create([
                                "expediente_id" => $expediente->id,
                                "multiple" => $multiple,
                                "fecha_audiencia" => $datos_audiencia["fecha_audiencia"],
                                "fecha_limite_audiencia" => $fecha_notificacion,
                                "hora_inicio" => $datos_audiencia["hora_inicio"],
                                "hora_fin" => $datos_audiencia["hora_fin"],
                                "conciliador_id" => $datos_audiencia["conciliador_id"],
                                "numero_audiencia" => 1,
                                "reprogramada" => false,
                                "anio" => $folioAudiencia->anio,
                                "folio" => $folioAudiencia->contador,
                                "encontro_audiencia" => $datos_audiencia["encontro_audiencia"],
                                "fecha_cita" => $fecha_cita,
                                "etapa_notificacion_id" => $etapa->id,
                    ]);
                    if ($datos_audiencia["encontro_audiencia"]) {
                        // guardamos la sala y el consiliador a la audiencia
                        ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $datos_audiencia["conciliador_id"], "solicitante" => true]);
                        SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $datos_audiencia["sala_id"], "solicitante" => true]);
                        if ($request->separados == "true") {
                            ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $datos_audiencia["conciliador2_id"], "solicitante" => false]);
                            SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $datos_audiencia["sala2_id"], "solicitante" => false]);
                        }
                    }
                    // Guardamos todas las Partes en la audiencia
                    //                dd($partes);

                    foreach ($partes as $parte) {
                        if ($parte->tipo_parte_id != 1) {
                            $tipo_notificacion_id = $this->request->tipo_notificacion_id;
                        }
                        AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parte->id, "tipo_notificacion_id" => $tipo_notificacion_id]);
                        if ($parte->tipo_parte_id == 2 && $datos_audiencia["encontro_audiencia"]) {
                            event(new GenerateDocumentResolution($audiencia->id, $solicitud->id, 14, 4, null, $parte->id));
                        }
                    }
                    //                if($datos_audiencia["encontro_audiencia"] && ($tipo_notificacion_id != 1 && $tipo_notificacion_id != null)){
                    //                    event(new RatificacionRealizada($audiencia->id,"citatorio"));
                    //                }
                    $expediente = Expediente::find($request->expediente_id);
                }

                $salas = [];
                foreach ($audiencia->salasAudiencias as $sala) {
                    $sala->sala;
                }
                foreach ($audiencia->conciliadoresAudiencias as $conciliador) {
                    $conciliador->conciliador->persona;
                }
                $acuse = Documento::where('documentable_type', 'App\Solicitud')->where('documentable_id', $solicitud->id)->where('clasificacion_archivo_id', 40)->first();
                if ($acuse != null) {
                    $acuse->delete();
                }
                DB::commit();
                if ($request->inmediata != "true" && $audiencia->encontro_audiencia && ($tipo_notificacion_id != 1 && $tipo_notificacion_id != null)) {
                    event(new RatificacionRealizada($audiencia->id, "citatorio"));
                }
                event(new GenerateDocumentResolution("", $solicitud->id, 40, 6));
                return $audiencia;
            } else {
                DB::rollback();
                return $solicitud->expediente->audiencia()->with('audienciaParte','conciliadoresAudiencias','conciliadoresAudiencias.conciliador.persona','salasAudiencias','salasAudiencias.sala')->first();;
            }
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al confirmar la solicitud', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al confirmar la solicitud');
        }
//        catch (\GuzzleHttp\Exception\ClientException $e) {
//            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
//                       " Se emitió el siguiente mensale: ". $e->getMessage().
//                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
//            DB::rollback();
//            if ($this->request->wantsJson()) {
//                return $this->sendError('Error al enviar las notificaciones', 'Error');
//            }
//            return redirect('solicitudes')->with('error', 'Error al enviar las notificaciones');
//        }
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

    function ExcepcionConciliacion(Request $request) {

        $solicitud_id = $request->solicitud_id_excepcion;
        $solicitud = Solicitud::find($solicitud_id);
        $files = $request->file();
        foreach ($files as $parte_id => $archivo) {
            $clasificacion_archivo = 7;
            $parte = Parte::find($parte_id);
            if ($solicitud != null) {
                $directorio = 'expedientes/' . $solicitud->expediente->id . '/solicitud/' . $solicitud_id . '/parte/' . $parte->id;
                Storage::makeDirectory($directorio);
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                $path = $archivo->store($directorio);
                $uuid = Str::uuid();
                $parte->documentos()->create([
                    "nombre" => str_replace($directorio . "/", '', $path),
                    "nombre_original" => str_replace($directorio, '', $archivo->getClientOriginalName()),
                    "descripcion" => "Documento de audiencia " . $tipoArchivo->nombre,
                    "ruta" => $path,
                    "uuid" => $uuid,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => $tipoArchivo->id,
                ]);
            }
        }

        $solicitados = Parte::where('solicitud_id', $solicitud_id)->where('tipo_parte_id', 2)->get();
        foreach ($solicitados as $key => $solicitado) {
            foreach ($request->files as $solicitante_id => $file) {
                ResolucionParteExcepcion::create(['parte_solicitante_id' => $solicitante_id, 'parte_solicitada_id' => $solicitado->id, 'conciliador_id' => $request->conciliador_excepcion_id, 'resolucion_id' => 3]);
                // generar constancia de excepcion a la conciliacion
                event(new GenerateDocumentResolution("", $solicitud_id, 2, 5, $solicitante_id, $solicitado->id, $request->conciliador_excepcion_id));
            }
        }
        $solicitud->estatus_solicitud_id = 3;
        $solicitud->update();
        return redirect('solicitudes')->with('success', 'Se guardo todo');
    }

    function getDocumentosSolicitud($solicitud_id) {
        $doc = collect();
        $solicitud = Solicitud::find($solicitud_id);
        $documentos = $solicitud->documentos;
        foreach ($documentos as $documento) {
            if ($documento->ruta != "") {
                $documento->id = $documento->id;
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta)['extension'];
                $documento->uuid = $documento->uuid;
                $doc->push($documento);
            }
        }
        $partes = Parte::where('solicitud_id', $solicitud_id)->get();
        foreach ($partes as $parte) {

            $documentos = $parte->documentos;
            foreach ($documentos as $documento) {
                $documento->id = $documento->id;
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta)['extension'];
                $documento->parte = $parte->nombre . " " . $parte->primer_apellido . " " . $parte->segundo_apellido;
                $documento->uuid = $documento->uuid;
                $doc->push($documento);
            }
        }
        $documentos = $doc->sortBy('id');
        return $documentos;
    }

    function getAcuseSolicitud($solicitud_id) {
        $doc = [];
        $solicitud = Solicitud::find($solicitud_id);
        if ($solicitud != null) {
            $documentos = $solicitud->documentos;
            foreach ($documentos as $documento) {
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                if ($documento->clasificacionArchivo->id == 40) {
                    $documento->tipo = pathinfo($documento->ruta)['extension'];
                    array_push($doc, $documento);
                }
            }
            return $doc;
        }
        return $this->sendError('No se puede obtener el acuse', 'Error');
    }

    private function getAcciones(Solicitud $solicitud, $partes, $audiencias, $expediente) {
//         Obtenemos las acciones de la solicitud
        $SolicitudAud = $solicitud->audits()->get();
//        Obtenemos las acciones de las partes
        foreach ($partes as $parte) {
            $SolicitudAud = $SolicitudAud->merge($parte->audits()->get());
        }
//        Obtenemos las acciones de las audiencias
        foreach ($audiencias as $audiencia) {
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
            if ($audit->auditable_type == 'App\Parte') {
                $table = "Parte";
                $parte = Parte::find($audit->auditable_id);
                if ($parte->tipo_persona_id == 1) {
                    $extra = $parte->nombre . " " . $parte->primer_apellido . " " . $parte->segundo_apellido;
                } else {
                    $extra = $parte->nombre_comercial;
                }
            } else if ($audit->auditable_type == 'App\Audiencia') {
                $table = "Audiencia";
            } else if ($audit->auditable_type == 'App\Expediente') {
                $table = "Expediente";
                $expediente = Expediente::find($audit->auditable_id);
                $extra = $expediente->folio . "/" . $expediente->anio;
            }
            $nombre = "Sin dato";
            if ($audit->user_id != null) {
                $user = User::find($audit->user_id);
                $nombre = $user->persona->nombre . " " . $user->persona->primer_apellido . " " . $user->persona->segundo_apellido;
            }
            $audits[] = array("user" => $nombre, "elemento" => $table, "extra" => $extra, "event" => $audit->event, "created_at" => $audit->created_at, "cambios" => $audit->getModified());
        }
        return $audits;
    }

    public function validarCorreos() {
        $solicitud = Solicitud::find($this->request->solicitud_id);
        $array = array();
        foreach ($solicitud->partes as $parte) {
            if ($parte->tipo_parte_id == 1) {
                $pasa = false;
                foreach ($parte->contactos as $contacto) {
                    if ($contacto->tipo_contacto_id == 3) { //si tiene email
                        $pasa = true;
                    }
                }
                if (!$pasa) {//devuelve partes sin email
                    if ($parte->correo_buzon == null || $parte->correo_buzon == "") {
                        $array[] = $parte;
                    }
                }
            }
        }
        return $array;
    }

    public function cargarCorreos() {
        try {
            DB::beginTransaction();
            foreach ($this->request->listaCorreos as $listaCorreos) {
                $parte = Parte::find($listaCorreos["parte_id"]);
                if ($listaCorreos["crearAcceso"]) {
                    $arrayCorreo = $this->construirCorreo($parte);
                    $parte->update([
                        "correo_buzon" => $arrayCorreo["correo"],
                        "password_buzon" => $arrayCorreo["password"]
                    ]);
                } else {
                    $parte->contactos()->create([
                        "tipo_contacto_id" => 3,
                        "contacto" => $listaCorreos["correo"]
                    ]);
                }
            }
            DB::commit();
            return $this->sendResponse("success", "Se guardaron los correos");
        } catch (\Throwable $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al guardar los correos', 'Error');
        }
    }

    private function construirCorreo(Parte $parte) {
        $password = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        if ($parte->tipo_persona_id == 1) {
            $correo = str_replace(' ', '', $parte->curp) . "@mibuzonlaboral.gob.mx";
        } else {
            $correo = str_replace(' ', '', $parte->rfc) . "@mibuzonlaboral.gob.mx";
        }
        return ["correo" => strtolower($correo), "password" => strtolower($password)];
    }

    private static function validarCentroAsignacion() {
        $pasa = false;
        $pasaSala = false;
        $pasaConciliador = false;
        if (count(auth()->user()->centro->disponibilidades) > 0) {
            foreach (auth()->user()->centro->salas as $sala) {
                if (count($sala->disponibilidades) > 0) {
                    if (!$sala->virtual) {
                        $pasaSala = true;
                    }
                }
            }
            if ($pasaSala) {
                foreach (auth()->user()->centro->conciliadores as $conciliador) {
                    if (count($conciliador->disponibilidades) > 0) {
                        $pasaConciliador = true;
                    }
                }
            }
        }
        if ($pasaSala && $pasaConciliador) {
            $pasa = true;
        }
        return $pasa;
    }

    public function ReenviarNotificacion() {
        //Buscamos las solicitudes que no tengan fecha_peticion_notificacion
        try {
            $query = Solicitud::where("fecha_peticion_notificacion", null);
            if ($this->request->get('centro_id')) {
                $query->where('centro_id', $this->request->get('centro_id'));
            }
            $solicitudes = $query->get();

            var_dump("La transaccion inicia a las: " . date("H:i:s") . "\n");
            $enviadas = 0;
            foreach ($solicitudes as $solicitud) {
                DB::beginTransaction();
                if (isset($solicitud->expediente->audiencia)) {
                    //Obtenemos la audiencia
                    foreach ($solicitud->expediente->audiencia as $audiencia) {
                        $notificar = false;
                        foreach ($audiencia->audienciaParte as $parte) {
                            if ($parte->parte && $parte->parte->tipo_parte_id != 1) {
                                if ($parte->tipo_notificacion_id != 1 && $parte->tipo_notificacion_id != null) {
                                    $notificar = true;
                                }
                            } else {
                                var_dump("No hay parte. revisar de que se trata: SID:" . $solicitud->id . "\n");
                            }
                        }
                        if ($notificar) {
                            event(new RatificacionRealizada($audiencia->id, "citatorio"));
                            $enviadas++;
                        }
                        var_dump("se notifica la audiencia: " . $audiencia->id . ": " . $audiencia->folio . "/" . $audiencia->anio . "\n");
                    }
                }
                DB::commit();
            }
            var_dump("La transaccion termina a las: " . date("H:i:s") . "\n");
            var_dump("Se enviaron: " . $enviadas . " solicitudes");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
        }
    }

    public function incidencias_solicitudes() {
        try {
            $solicitudes = Solicitud::where('incidencia', true)->with('partes', 'tipoIncidenciaSolicitud', 'solicitud', 'centro','expediente.audiencia');
            $rolActual = session('rolActual')->name;
            if ($rolActual == 'Orientador Central') {
                $solicitudes->whereRaw('(tipo_solicitud_id = 3 or tipo_solicitud_id = 4)');
            } else if ($rolActual != 'Super Usuario') {
                $centro_id = Auth::user()->centro_id;
                $solicitudes->where('centro_id', $centro_id);
            } 
            if($rolActual == "Personal conciliador"){
                $conciliador_id = auth()->user()->persona->conciliador->id;
                $solicitudes->whereHas('expediente.audiencia',function ($query) use ($conciliador_id) { $query->where('conciliador_id',$conciliador_id); });
            }
            $solicitudes = $solicitudes->get();
            $tipoIncidenciaSolicitud = $this->cacheModel('tipo_incidencia_solicitudes', TipoIncidenciaSolicitud::class);
            return view('herramientas.incidencias_solicitudes', compact('tipoIncidenciaSolicitud', 'solicitudes'));
        } catch (Exception $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensaje: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            $solicitudes = Solicitud::where('incidencia', true)->with('partes', 'tipoIncidenciaSolicitud')->get();
            $tipoIncidenciaSolicitud = $this->cacheModel('tipo_incidencia_solicitudes', TipoIncidenciaSolicitud::class);
            return view('herramientas.incidencias_solicitudes', compact('tipoIncidenciaSolicitud', 'solicitudes'));
        }
    }

    public function guardar_incidencia(Request $request) {
        DB::beginTransaction();
        try {
            $user_id = Auth::user()->id;
            $solicitud = Solicitud::find($request->solicitud_id);
            $solicitud->incidencia = true;
            $solicitud->fecha_incidencia = now();
            $solicitud->tipo_incidencia_solicitud_id = $request->tipo_incidencia_solicitud_id;
            $solicitud->justificacion_incidencia = $request->justificacion_incidencia;
            $solicitud->user_id = $user_id;
            $solicitud->save();
            if ($request->solicitud_asociada_id) {
                $solicitud->solicitud_id = $request->solicitud_asociada_id;
            }
            if ($request->tipo_incidencia_solicitud_id == 4 || $request->tipo_incidencia_solicitud_id == 6) {
                if ($solicitud->expediente) {
                    $audiencia = $solicitud->expediente->audiencia()->orderBy('id', 'desc')->first();
                    if ($audiencia) {
                        //$response = HerramientaServiceProvider::rollback($solicitud->id,$audiencia->id,2);
                        // if($response["success"]){
                        $solicitud->estatus_solicitud_id = 3;
                        $solicitud->incidencia = true;
                        $solicitud->fecha_incidencia = now();
                        $solicitud->save();
                        $partes = $solicitud->partes;
                        foreach ($partes as $parte) {
                            if ($parte->tipo_parte_id == 1) {
                                //generar constancia de incompetencia por solicitante
                                event(new GenerateDocumentResolution(null, $solicitud->id, 13, 10, $parte->id, null));
                            }
                        }
                        // }else{
                        //     DB::rollback();
                        //     return $this->sendError(' Error no se pudo guardar la incidencia ', 'Error');
                        // }
                    } else {
                        $solicitud->estatus_solicitud_id = 3;
                        $solicitud->incidencia = true;
                        $solicitud->fecha_incidencia = now();
                        $solicitud->save();
                        $partes = $solicitud->partes;
                        foreach ($partes as $parte) {
                            if ($parte->tipo_parte_id == 1) {
                                //generar constancia de incompetencia por solicitante
                                event(new GenerateDocumentResolution(null, $solicitud->id, 13, 10, $parte->id, null));
                            }
                        }
                    }
                } else {
                    DB::rollback();
                    return $this->sendError(' Esta solicitud no tiene audiencias, crear incompetencia en proceso de confirmación ', 'Error');
                }
            }
            
            if($request->tipo_incidencia_solicitud_id == 7){
                if ($solicitud->expediente && $solicitud->expediente->audiencia) {
                    event(new GenerateDocumentResolution($solicitud->expediente->audiencia()->orderBy('id','desc')->first()->id,$solicitud->id,61,24,null,null));
                }else{
                    DB::rollback();
                    return $this->sendError(' Esta solicitud no esta confirmada, no se puede realizar este proceso ', 'Error');
                }
            }
            
            DB::commit();
            return $this->sendResponse($solicitud, 'SUCCESS');
        } catch (Exception $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensaje: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            return $this->sendError(' Error no se pudo guardar la incidencia ', 'Error');
        }
    }

    public function borrar_incidencia(Request $request) {
        DB::beginTransaction();
        try {
            $solicitud = Solicitud::find($request->solicitud_id);
            $solicitud->incidencia = false;
            $solicitud->tipo_incidencia_solicitud_id = null;
            $solicitud->justificacion_incidencia = null;
            $solicitud->solicitud_id = null;
            $solicitud->save();
            DB::commit();
            return $this->sendResponse($solicitud, 'SUCCESS');
        } catch (Exception $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensaje: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            DB::rollback();
            return $this->sendError(' Error no se pudo guardar la incidencia ', 'Error');
        }
    }

    public function deshacer_solicitudes() {
        return view('herramientas.deshacer_procesos');
    }

    public function rollback_proceso(Request $request) {
        try {
            $solicitud_id = $request->solicitud_id;
            $audiencia_id = $request->audiencia_id;
            $tipoRollback = $request->tipoRollback;
            $response = HerramientaServiceProvider::rollback($solicitud_id, $audiencia_id, $tipoRollback);
            if ($response["success"]) {
                return $this->sendResponse(null, $response["msj"]);
            } else {
                return $this->sendError($response["msj"]);
            }
        } catch (Exception $e) {
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensaje: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return $this->sendError(' Error no se pudo guardar la incidencia ', 'Error');
        }
    }
    public function delete_audiencia(Request $request){
        try{
            $solicitud_id = $request->solicitud_id;
            $audiencia_id = $request->audiencia_id;
            $response = HerramientaServiceProvider::delete_audiencia($solicitud_id,$audiencia_id);
            if($response["success"]){
                return $this->sendResponse(null, $response["msj"]);
            }else{
                return $this->sendError($response["msj"]);
            }
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                " Se emitió el siguiente mensaje: ". $e->getMessage().
                " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return $this->sendError(' Error, no se pudo eliminar la audiencia ', 'Error');
        }
    }

    public function canal(Request $request) {
        $mensaje = "El canal ingresado no es el correcto, verifica el canal asignado en tus documentos";
        $solicitud = Solicitud::where('canal', $request->canal)->first();
        if ($solicitud) {
            if (!empty($solicitud->url_virtual)) {
                return Redirect::to($solicitud->url_virtual);
            }
            $mensaje = "No ha iniciado aún su videollamada programada con el funcionario del CFCRL. Favor de revisar la fecha y hora asignadas para la videollamada y entrar a esta liga en ese momento";
        }
        return view('pages.canalNotFound', compact('mensaje'));
    }

    public function identificacion(Request $request) {
        $arrResponse = [];
        $archivo = $request->file;
        $directorio = "solicitudes/tmp";
        Storage::makeDirectory($directorio);
        $path = $archivo->store($directorio);
        array_push($arrResponse, $path);
        if ($request->file2) {
            $archivo2 = $request->file2;
            $directorio = "solicitudes/tmp";
            Storage::makeDirectory($directorio);
            $path2 = $archivo2->store($directorio);
            array_push($arrResponse, $path2);
        }
        return $this->sendResponse($arrResponse, 'SUCCESS');
    }

    public function guardarUrlVirtual(Request $request){
        try{
            $solicitud = Solicitud::find($request->solicitud_id);
            $solicitud->url_virtual = $request->url_virtual;
            $solicitud->save();
            return $this->sendResponse("", 'SUCCESS');
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                " Se emitió el siguiente mensaje: ". $e->getMessage().
                " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return $this->sendError(' Error no se pudo guardar la url ', 'Error');
        }
    }
    public function eliminar_audiencias(){
        return view('herramientas.eliminar_audiencias');
    }
    public function showPorCaducar(){
        $solicitudes = HerramientaServiceProvider::getSolicitudesPorCaducar(true);
        return view('expediente.solicitudes.porCaducar',compact("solicitudes"));
    }
}
