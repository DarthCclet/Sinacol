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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            if ($this->request->get('Expediente')) {
                $expediente = Expediente::where('folio', $this->request->get('Expediente'))->first();
                if (count($expediente) > 0) {
                    $solicitud->where('id', $expediente->solicitud->id);
                } else {
                    $solicitud->where('id', '<', '1');
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
            if ($this->request->get('IsDatatableScroll')) {
                $solicitud = $solicitud->orderBy("fecha_recepcion", 'desc')->take($length)->skip($start)->get();
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
                $total = Solicitud::count();
                $draw = $this->request->get('draw');
                return $this->sendResponseDatatable($total, $total, $draw, $solicitud, null);
            }
        }
        return view('expediente.solicitudes.index', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


        $objeto_solicitudes = $this->cacheModel('objeto_solicitudes',ObjetoSolicitud::class);
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
        // $municipios = $this->cacheModel('municipios',Municipio::class,'municipio');
        //$municipios = array_pluck(Municipio::all(),'municipio','id');
        $municipios=[];
        return view('expediente.solicitudes.create', compact('objeto_solicitudes','estatus_solicitudes','tipos_vialidades','tipos_asentamientos','estados','jornadas','generos','nacionalidades','giros_comerciales','ocupaciones','lengua_indigena','tipo_contacto','periodicidades','municipios','grupos_prioritarios','motivo_excepcion'));
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

        $request->validate([
            'solicitud.fecha_conflicto' => 'required',
            'solicitud.solicita_excepcion' => 'required',
            'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.rfc' => ['nullable', new RFC],
            'solicitantes.*.tipo_parte_id' => 'required',
            'solicitantes.*.tipo_persona_id' => 'required',
            'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
            'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
            'solicitantes.*.entidad_nacimiento_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.dato_laboral' => 'required',
            'solicitantes.*.domicilios' => 'required',
            'solicitantes.*.contactos' => 'required',
            'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
            'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
            'solicitados.*.rfc' => ['nullable', new RFC],
            'solicitados.*.tipo_parte_id' => 'required',
            'solicitados.*.tipo_persona_id' => 'required',
            'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
            'solicitados.*.domicilios' => 'required'
        ]);

        $solicitud = $request->input('solicitud');

        DB::beginTransaction();
        $domiciliop ="";
        try {
            // Solicitud
            $solicitud['user_id'] = 1;
            $solicitud['estatus_solicitud_id'] = 1;
            $date = new \DateTime();
            $solicitud['fecha_recepcion'] = $date->format('Y-m-d H:i:s');
            $solicitud['centro_id'] = $this->getCentroId();
            //Obtenemos el contador
            $ContadorController = new ContadorController();
            $folio = $ContadorController->getContador(1, 1);
            $solicitud['folio'] = $folio->contador;
            $solicitud['anio'] = $folio->anio;
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
                $dato_laboral = $value['dato_laboral'];

                unset($value['dato_laboral']);
                if (isset($value["domicilios"])) {
                    $domicilio = $value["domicilios"][0];
                    unset($value['domicilios']);
                }
                if (isset($value["contactos"])) {
                    $contactos = $value["contactos"];
                    unset($value['contactos']);
                }

                // dd($value);
                $parteSaved = (Parte::create($value)->dato_laboral()->create($dato_laboral)->parte);
                // dd($domicilio);
                // foreach ($domicilios as $key => $domicilio) {
                unset($domicilio['activo']);
                $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
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
                    if($key == 0){
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
        } catch (\Throwable $e) {
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
        return $solicitud;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
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
        $objeto_solicitudes = $this->cacheModel('objeto_solicitudes', ObjetoSolicitud::class);
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
        $audits = $this->getAcciones($solicitud, $parte->get(), $audiencias,$expediente);
        $municipios = array_pluck(Municipio::all(),'municipio','id');
        $motivo_excepciones = $this->cacheModel('motivo_excepcion',MotivoExcepcion::class);
        $clasificacion_archivo = ClasificacionArchivo::all();
        // dd(Conciliador::all()->persona->full_name());
        $conciliadores = array_pluck(Conciliador::with('persona')->get(),"persona.nombre",'id');
        // dd($conciliador);
        // $conciliadores = $this->cacheModel('conciliadores',Conciliador::class);
        return view('expediente.solicitudes.edit', compact('solicitud', 'objeto_solicitudes', 'estatus_solicitudes', 'tipos_vialidades', 'tipos_asentamientos', 'estados', 'jornadas', 'generos', 'nacionalidades', 'giros_comerciales', 'ocupaciones', 'expediente', 'audiencias', 'grupo_prioritario', 'lengua_indigena', 'tipo_contacto', 'periodicidades', 'audits','municipios','partes','motivo_excepciones','conciliadores','clasificacion_archivo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitud $solicitud) {
        $request->validate([
            'solicitud.fecha_conflicto' => 'required',
            'solicitud.solicita_excepcion' => 'required',
            'solicitantes.*.nombre' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.primer_apellido' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.rfc' => ['nullable', new RFC],
            'solicitantes.*.tipo_parte_id' => 'required',
            'solicitantes.*.tipo_persona_id' => 'required',
            'solicitantes.*.curp' => ['exclude_if:solicitantes.*.tipo_persona_id,2|required', new Curp],
            'solicitantes.*.edad' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required|Integer',
            'solicitantes.*.entidad_nacimiento_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.fecha_nacimiento' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.genero_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.nacionalidad_id' => 'exclude_if:solicitantes.*.tipo_persona_id,2|required',
            'solicitantes.*.dato_laboral' => 'required',
            'solicitantes.*.domicilios' => 'required',
            'solicitantes.*.contactos' => 'required',
            'solicitados.*.nombre' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
            'solicitados.*.primer_apellido' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
            'solicitados.*.rfc' => ['nullable', new RFC],
            'solicitados.*.tipo_parte_id' => 'required',
            'solicitados.*.tipo_persona_id' => 'required',
            'solicitados.*.curp' => ['exclude_if:solicitados.*.tipo_persona_id,2|nullable', new Curp],
            'solicitados.*.genero_id' => 'exclude_if:solicitados.*.tipo_persona_id,2|required',
            'solicitados.*.domicilios' => 'required',
        ]);
        $solicitud = $request->input('solicitud');
        DB::beginTransaction();
        try {
            // Solicitud
            $solicitud['user_id'] = 1;
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
                    $dato_laboral = $value['dato_laboral'];

                    unset($value['dato_laboral']);
                    if (isset($value["domicilios"])) {
                        $domicilio = $value["domicilios"][0];
                        unset($value['domicilios']);
                    }
                    if (isset($value["contactos"])) {
                        $contactos = $value["contactos"];
                        unset($value['contactos']);
                    }


                    if (!isset($value["id"]) || $value["id"] == "") {
                        $parteSaved = (Parte::create($value)->dato_laboral()->create($dato_laboral)->parte);
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
                        if (isset($dato_laboral["id"]) && $dato_laboral["id"] != "") {
                            $dato_laboralUp = DatoLaboral::find($dato_laboral["id"]);
                            $dato_laboralUp->update($dato_laboral);
                        } else {
                            $dato_laboral = ($parteSaved->dato_laboral()->create($dato_laboral));
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
            DB::rollback();
            if ($this->request->wantsJson()) {

                return $this->sendError('Error');
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

    public function Ratificar(Request $request) {
        DB::beginTransaction();
        try{
            $solicitud = Solicitud::find($request->id);
            //Indicamos que la solicitud ha sido ratificada
            $solicitud->update(["estatus_solicitud_id" => 2, "ratificada" => true, "fecha_ratificacion" => now()]);
            //Obtenemos el contador
            $ContadorController = new ContadorController();
            $folioC = $ContadorController->getContador(1,$solicitud->centro->id);
            $edo_folio = $solicitud->centro->abreviatura;
            $folio = $edo_folio. "/CJ/I/". $folioC->anio."/".sprintf("%06d", $folioC->contador);
            //Creamos el expediente de la solicitud
            $expediente = Expediente::create(["solicitud_id" => $request->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
            foreach ($solicitud->partes as $key => $parte) {
                if($parte->documentos != null){
                    $parte->ratifico = true;
                    $parte->update();
                }else{
                }
            }
            //guardamos el tipo de notificacion de las partes
//            foreach($request->listaNotificaciones as $notificaciones){
//                $parte = Parte::find($notificaciones["parte_id"])->update(["tipo_notificacion_id" => $notificaciones["tipo_notificacion_id"]]);
//            }
            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            dd($e);
            if ($this->request->wantsJson()) {
                return $this->sendError('Error al ratificar la solicitud', 'Error');
            }
            return redirect('solicitudes')->with('error', 'Error al ratificar la solicitud');
        }
        return $solicitud;
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
            $documento->clasificacionArchivo = $documento->clasificacionArchivo;
            $documento->tipo = pathinfo($documento->ruta)['extension'];
            array_push($doc,$documento);
        }
        $partes = Parte::where('solicitud_id',$solicitud_id)->get();
        foreach($partes as $parte){

            $documentos = $parte->documentos;
            foreach ($documentos as $documento) {
                $documento->clasificacionArchivo = $documento->clasificacionArchivo;
                $documento->tipo = pathinfo($documento->ruta)['extension'];
                array_push($doc,$documento);
            }
        }
        return $doc;
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

}
