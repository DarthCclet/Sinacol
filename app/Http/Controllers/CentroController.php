<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Centro;
use App\Disponibilidad;
use App\Estado;
use App\Municipio;
use App\Incidencia;
use App\Filters\CatalogoFilter;
use App\TipoAsentamiento;
use App\TipoVialidad; 
use App\Audiencia;
use App\CentroMunicipio;
use App\Domicilio;
use App\Solicitud;
use App\TipoNotificacion;
use App\AudienciaParte;
use App\Events\RatificacionRealizada;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use OwenIt\Auditing\Models\Audit;

class CentroController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        // $this->middleware('auth');
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Filtramos los centros con los parametros que vengan en el request
        $centros = (new CatalogoFilter(Centro::query(), $this->request))
            ->searchWith(Centro::class)
            ->filter(false);
        if(!auth()->user()->hasRole('Super Usuario')){
            $centros->where("id",auth()->user()->centro_id);
        }
        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $archivo_csv = 'CatalogoCentros.csv';
            $query = $centros;
            $query->select(["id","nombre","duracionAudiencia","abreviatura","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $query = $query->withTrashed()->get();
            return $this->sendCSVResponse($query->toArray(),['id','nombre',"duracionAudiencia","abreviatura",'creado','modificado','eliminado'], $archivo_csv);
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $centros = $centros->get();
        } else {
            $centros->select("id","nombre","duracionAudiencia","abreviatura","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $centros = $centros->orderby('nombre')->paginate($this->request->get('per_page', 10));
        }

        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()) {

            return $this->sendResponse($centros, 'SUCCESS');
        }
        $tipos_vialidades = $this->cacheModel('tipos_vialidades',TipoVialidad::class);
        $tipos_asentamientos = $this->cacheModel('tipos_asentamientos',TipoAsentamiento::class);
        $estados = Estado::all();
        return view('centros.centros.index', compact('centros','estados','tipos_asentamientos','tipos_vialidades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tipos_vialidades = array_pluck(TipoVialidad::all(),'nombre','id');
        $tipos_asentamientos = array_pluck(TipoAsentamiento::all(),'nombre','id');
        $estados = Estado::all();
        $municipios = array_pluck(Municipio::all(),'municipio','id');
        return view('centros.centros.create', compact('estados','tipos_asentamientos','tipos_vialidades','municipios'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $centro = Centro::create($request->input('centro'));
        $domicilio = $request->input('domicilio');
        $centro->domicilio()->create($domicilio);
        return redirect('centros');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Centro $centro)
    {
        return $centro;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Centro $centro)
    {
        $municipio_id = "";
        $tipos_vialidades = array_pluck(TipoVialidad::all(),'nombre','id');
        $tipos_asentamientos = array_pluck(TipoAsentamiento::all(),'nombre','id');
        $estados = Estado::all();
        $municipios = array_pluck(Municipio::all(),'municipio','id');
        $tipo_contactos = array_pluck(\App\TipoContacto::all(),'nombre','id');
        $tipo_atencion_centro = array_pluck(\App\TipoAtencionCentro::all(),'nombre','id');
        if($centro->domicilio){
            $municipio_nombre = mb_strtoupper($centro->domicilio->municipio);
            $municipio_selected = Municipio::where('municipio','like','%'.$municipio_nombre.'%')->first();
            if($municipio_selected){
                $municipio_id = $municipio_selected->id;
            }
        }
        return view('centros.centros.edit', compact('centro','estados','tipos_asentamientos','tipos_vialidades','municipios','municipio_id','tipo_contactos','tipo_atencion_centro'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Centro $centro)
    {
        $centroRequest = $request->input('centro');
        if(!isset($centroRequest['sedes_multiples'])){
            $centroRequest['sedes_multiples'] = false;
        }else{
            $centroRequest['sedes_multiples'] = true;
        }
        $centro->update($centroRequest);
        $domicilio = $request->input('domicilio');
        if($domicilio['id'] != null && $domicilio['id'] != ""){
            Domicilio::find($domicilio['id'])->update($domicilio);
        }else{
            $centro->domicilio()->create($domicilio);

        }
        return redirect('centros');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Centro $centro)
    {
        $centro->delete();
        return redirect('centros');
    }

    /**
     * Función para guardar modificar y eliminar disponibilidades
     * @param Request $request
     * @return Centro $centro
     */
    public function disponibilidad(Request $request){
        $centro = Centro::find($request->id);
        foreach($request->datos as $value){
            if(!$value["borrar"] || $value["borrar"] == 'false'){
                if($value["disponibilidad_id"] != ""){
                    $disponibilidad = Disponibilidad::find($value["disponibilidad_id"]);
                    $disponibilidad->update(['dia' => $value["dia"],'hora_inicio' => $value["hora_inicio"],'hora_fin' => $value["hora_fin"]]);
                }else{
                    $centro->disponibilidades()->create(['dia' => $value["dia"],'hora_inicio' => $value["hora_inicio"],'hora_fin' => $value["hora_fin"]]);
                }
            }else{
                $disponibilidad = Disponibilidad::find($value["disponibilidad_id"])->delete();
            }
        }
        return $centro;
    }

    /**
     * Funcion para guardar y modificar incidencias
     * @param Request $request
     * @return Centro $centro
     */
    public function incidencia(Request $request){
        $centro = Centro::find($request->id);
        if($request->incidencia_id == ""){
            $centro->incidencias()->create(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio." 00:00:00","fecha_fin" => $request->fecha_fin." 23:59:00"]);
        }else{
            $incidencia = Incidencia::find($request->incidencia_id);
            $incidencia->update(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio." 00:00:00","fecha_fin" => $request->fecha_fin." 23:59:00"]);
        }
        return $centro;
    }

    /**
     * Funcion para obtener el objeto centro con sus disponibilidades e incidencias
     * @param Request $request
     * @return type
     */
    public function getDisponibilidades(Request $request){
        $centro = Centro::find($request->id);
        $centro->disponibilidades = $centro->disponibilidades;
        $centro->incidencias = $centro->incidencias;
        return $centro;
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
    public function getAudienciasCalendario(){
        $audiencias = array();
        $solicitudes = Solicitud::where("centro_id", auth()->user()->centro_id)->where("ratificada",true)->whereIn("tipo_solicitud_id",[1,2])->with(["expediente","expediente.audiencia"])->get();
        foreach($solicitudes as $solicitud){
            if(!$solicitud->incidencia){
                foreach($solicitud->expediente->audiencia as $audiencia){
                    if(!$audiencia->encontro_audiencia){
                        $audiencias[]=$audiencia;
                    }
                }
            }
        }
        return view("centros.centros.calendario_audiencias", compact('audiencias'));
    }
    public function CalendarioColectivas(){
        $audiencias = array();
        $solicitudes = Solicitud::where("ratificada",true)->whereIn("tipo_solicitud_id",[3,4])->get();
        foreach($solicitudes as $solicitud){
            if(!$solicitud->incidencia){
                foreach($solicitud->expediente->audiencia as $audiencia){
                    if(!$audiencia->encontro_audiencia){
                        $audiencias[]=$audiencia;
                    }
                }
            }
        }
        return view("centros.centros.calendario_audiencias_colectivas", compact('audiencias'));
    }
    public function pruebaEvents(){
        try{
        event(new RatificacionRealizada(1));
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            dd($e);
        }
    }
    
    
    /**
     * Aqui comienzan las funciones de notificaciones
     */
    public function notificaciones(){
        $rolActual = session('rolActual')->name;
        $tipo_parte = \App\TipoParte::where("nombre","ilike","CITADO")->first();
        if($rolActual != "Orientador"){
            $solicitudesTodas = Solicitud::where("centro_id", auth()->user()->centro_id)->where("ratificada",true)->with(['partes','expediente','expediente.audiencia','expediente.audiencia.audienciaParte','expediente.audiencia.etapa_notificacion','expediente.audiencia.audienciaParte.parte','expediente.audiencia.audienciaParte.tipo_notificacion'])->get();
    //        dd($solicitudesTodas);
            $solicitudes = [];
            //En caso de ser conciliador buscamos el registro
            $conciliador_id = null;
            if($rolActual == "Personal conciliador"){
                $conciliador_id = auth()->user()->persona->conciliador->id;
            }
            foreach($solicitudesTodas as $solicitud){
                $tipo_notificacion_id = null;
                foreach($solicitud->expediente->audiencia as $audiencia){
                    if($audiencia->encontro_audiencia){
                        $notificada = true;
                        $partes = [];
                        foreach($audiencia->audienciaParte as $parte){
                            if($parte->parte != null){
                                if($parte->parte->tipo_parte_id == $tipo_parte->id){ 
                                    $parte->parte->fecha_notificacion = $parte->fecha_notificacion;
                                    $parte->parte->finalizado = $parte->finalizado;
                                    $parte->parte->audiencia_parte_id = $parte->id;
                                    if($parte->fecha_notificacion == null){
                                        $notificada = false;
                                    }
                                    $sol_array = array();
                                    $sol_array["id"] = $solicitud->id;
                                    $sol_array["folio"] = $solicitud->folio."/".$solicitud->anio;
                                    $sol_array["expediente"] = $solicitud->expediente->folio;
                                    $sol_array["fecha_peticion_notificacion"] = $solicitud->fecha_peticion_notificacion;
                                    $sol_array["tipo_notificacion_id"] = $parte->tipo_notificacion_id;
                                    $sol_array["tipo_notificacion"] = $parte->tipo_notificacion->nombre;
                                    $sol_array["reprogramada"] = $audiencia->reprogramada;
                                    $sol_array["audiencia"] = $audiencia->folio."/".$audiencia->anio;
                                    $sol_array["etapa_notificacion"] = $audiencia->etapa_notificacion->etapa;
                                    $sol_array["notificada"] = $notificada;
                                    $sol_array["parte"] = $parte->parte;
                                    $sol_array["audiencia_id"] = $audiencia->id;
                                    $sol_array["audiencia_parte_id"] = $parte->id;
                                    $solicitudes[] = $sol_array;   
                                }
                            }
                            $notificada = true;
                        }
                    }
                }
            }
            return view('centros.centros.notificaciones',compact('solicitudes'));
        }else{
            $audits = Audit::where("auditable_type","App\Audiencia")->where("event","Inserción")->where("user_id",auth()->user()->id)->get();
            $ids = [];
            foreach($audits as $audit){
                $ids[] = $audit->auditable_id;
            }
            $audiencias = Audiencia::whereIn('id',$ids)->with(['audienciaParte','expediente','expediente.solicitud','etapa_notificacion','audienciaParte.tipo_notificacion','audienciaParte.parte'])->get();
            $solicitudes = array();
            foreach($audiencias as $audiencia){
                if($audiencia->encontro_audiencia){
                    $notificada = true;
                    $partes = [];
                    foreach($audiencia->audienciaParte as $parte){
                        if($parte->parte != null){
                            if($parte->parte->tipo_parte_id == $tipo_parte->id){ 
                                $parte->parte->fecha_notificacion = $parte->fecha_notificacion;
                                $parte->parte->finalizado = $parte->finalizado;
                                $parte->parte->audiencia_parte_id = $parte->id;
                                if($parte->fecha_notificacion == null){
                                    $notificada = false;
                                }
                                $sol_array = array();
                                $sol_array["id"] = $audiencia->expediente->solicitud_id;
                                $sol_array["folio"] = $audiencia->expediente->solicitud->folio."/".$audiencia->expediente->solicitud->anio;
                                $sol_array["expediente"] = $audiencia->expediente->folio;
                                $sol_array["fecha_peticion_notificacion"] = $audiencia->expediente->solicitud->fecha_peticion_notificacion;
                                $sol_array["tipo_notificacion"] = $parte->tipo_notificacion->nombre;
                                $sol_array["tipo_notificacion_id"] = $parte->tipo_notificacion_id;
                                $sol_array["reprogramada"] = $audiencia->reprogramada;
                                $sol_array["audiencia"] = $audiencia->folio."/".$audiencia->anio;
                                $sol_array["etapa_notificacion"] = $audiencia->etapa_notificacion->etapa;
                                $sol_array["notificada"] = $notificada;
                                $sol_array["parte"] = $parte->parte;
                                $sol_array["audiencia_id"] = $audiencia->id;
                                $sol_array["audiencia_parte_id"] = $parte->id;
                                $solicitudes[] = $sol_array;   
                            }
                        }
                        $notificada = true;
                    }
                }
            }
            return view('centros.centros.notificaciones',compact('solicitudes'));
        }
    }
    public function obtenerHistorial(){
        $parte = AudienciaParte::find($this->request->audiencia_parte_id);
        if($parte != null){
            foreach($parte->historialNotificacion as $historico){
                foreach($historico->respuestas as $historico2){
                    $historico2->etapa_notificacion;
                    $historico2->documento;
                }
            }
            return $parte->historialNotificacion;
        }else{
            return null;
        }
    }
    public function EnviarNotificacion(){
        $audiencia = Audiencia::find($this->request->audiencia_id);
        $tipo_notificacion = TipoNotificacion::where("nombre","B) El notificador del centro entrega citatorio a citados")->first();
        foreach($audiencia->audienciaParte as $parte){
            if($parte->parte && $parte->parte->tipo_parte_id != 1){
                if($parte->tipo_notificacion_id == 1){
                    $parte->update(["tipo_notificacion_id" => $tipo_notificacion->id]);
                }
            }
        }
        $todos = true;
        if($this->request->audiencia_parte_id != null && $this->request->audiencia_parte_id != ""){
            $todos = false;
        }
        event(new RatificacionRealizada($audiencia->id, "citatorio",$todos,$this->request->audiencia_parte_id));
        
        
        
//        $solicitud = Solicitud::find($this->request->solicitud_id);
//        if(isset($solicitud->expediente->audiencia)){
//            //Obtenemos la audiencia
//            foreach($solicitud->expediente->audiencia as $audiencia){
//                $notificar = false;
//                $tipo_notificacion = TipoNotificacion::where("nombre","B) El notificador del centro entrega citatorio a citados")->first();
//                foreach($audiencia->audienciaParte as $parte){
//                    if($parte->parte && $parte->parte->tipo_parte_id != 1){
//                        if($parte->tipo_notificacion_id == 1){
//                            $parte->update(["tipo_notificacion_id" => $tipo_notificacion->id]);
//                        }
//                        $notificar = true;
//                    }
//                }
//                if($notificar){
//                    event(new RatificacionRealizada($audiencia->id, "citatorio",$this->request->parte_id));
//                }
//            }
//        }
//        $solicitud = Solicitud::find($this->request->solicitud_id);
        return $audiencia->expediente->solicitud;
    }
    /**
     * Aqui terminan las funciones de notificaciones
     */
    public function descargarCalendario(){
        $fecha_inicial = date($this->request->fecha_inicio);
        $fecha_fin = date($this->request->fecha_fin);
        $audienciasArray = Audiencia::join("expedientes","audiencias.expediente_id","expedientes.id")
            ->join("solicitudes","expedientes.solicitud_id","solicitudes.id")
            ->where("solicitudes.centro_id", auth()->user()->centro_id)
            ->where("audiencias.deleted_at",null)
            ->whereBetween("audiencias.fecha_audiencia",[$fecha_inicial,$fecha_fin])
            ->select("audiencias.*","solicitudes.folio as folio_solicitud","solicitudes.anio as anio_solicitud","expedientes.folio as folio_expediente")
            ->with(["salasAudiencias","conciliadoresAudiencias","salasAudiencias.sala","conciliadoresAudiencias.conciliador","conciliadoresAudiencias.conciliador.persona"])
            ->get();
        $audiencias = [];
        foreach($audienciasArray as $audiencia){
            $salas = self::obtenerSalas($audiencia->salasAudiencias);
            $conciliadores = self::obtenerConciliadores($audiencia->conciliadoresAudiencias);
            $estatus = "Por celebrar";
            if($audiencia->finalizada){
                $estatus = "Finalizada";
            }
            $audiencias[]=array(
                "Solicitud" => $audiencia->folio_solicitud."/".$audiencia->anio_solicitud,
                "Expediente" => $audiencia->folio_expediente,
                "Audiencia" => $audiencia->folio."/".$audiencia->anio,
                "Fecha de audiencia" => $audiencia->fecha_audiencia,
                "Hora de inicio" => $audiencia->hora_inicio,
                "Hora de termino" => $audiencia->hora_fin,
                "Conciliador(es)" => $conciliadores,
                "Sala(s)" => $salas,
                "Estatus" => $estatus
            );
        }
        $audiencias = collect($audiencias)->sortBy("Fecha de audiencia")->toArray();
        $arch = Carbon::now('America/Mexico_city')->format("Y-m-d_Hi");
        return (new FastExcel($audiencias))->download('Audiencias_'.$arch.'.xlsx');
    }
    public function obtenerSalas($salasAudiencias){

        $salasResponse = "";
        foreach($salasAudiencias as $sala){
            $salasResponse .=" ".$sala->sala->sala.",";
        }
        $salasResponse = substr($salasResponse,0,strlen($salasResponse)-1);
        return $salasResponse;
    }
    public function obtenerConciliadores($conciliadoresAudiencias){
        $conciliadoresResponse = "";
        foreach($conciliadoresAudiencias as $conciliador){
            $conciliadoresResponse .=" ".$conciliador->conciliador->persona->nombre." ".$conciliador->conciliador->persona->primer_apellido." ".$conciliador->conciliador->persona->segundo_apellido.",";
        }
        $conciliadoresResponse = substr($conciliadoresResponse,0,strlen($conciliadoresResponse)-1);
        return $conciliadoresResponse;
    }
    public function ObtenerContactos(){
        $centro = Centro::find($this->request->centro_id);
        return $centro->contactos()->with(['tipo_contacto'])->get();
    }
    public function AgregarContacto(){
        $centro = Centro::find($this->request->centro_id);
        $centro->contactos()->create(["tipo_contacto_id" => $this->request->tipo_contacto_id,"contacto" => $this->request->contacto]);
        return $centro;
    }
    public function EliminarContacto(){
        \App\Contacto::find($this->request->id)->delete();
        return $this->request->id;
    }
    public function getAtiendeVirtual($estado_id){
        $centro = Centro::find($estado_id);
        return $centro;
    }
}
