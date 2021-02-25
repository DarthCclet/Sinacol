<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Filters\CatalogoFilter;
use App\Conciliador;
use App\Persona;
use App\Disponibilidad;
use App\Incidencia;
use App\RolConciliador;
use Validator;
use Illuminate\Support\Collection;
class ConciliadorController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Conciliador::with('persona','centro')->get();

        // Filtramos las salas con los parametros que vengan en el request
        $conciliadores = (new CatalogoFilter(Conciliador::query(), $this->request))
            ->searchWith(Conciliador::class)
            ->filter()->get();
        $conciliadoresResponse = [];
        if(!auth()->user()->hasRole("Super Usuario")){
            foreach($conciliadores as $conciliador){
                if(isset($conciliador->persona->user)){
                    if($conciliador->persona->user->centro_id == auth()->user()->centro_id){
                        $conciliadoresResponse[] = $conciliador;
                    }
                }
                
            }
            $conciliadoresResponse = new Collection($conciliadoresResponse);
        }else{
            $conciliadoresResponse = $conciliadores;
        }
        $conciliadores = $conciliadoresResponse;
        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()){
            return $this->sendResponse($conciliadores, 'SUCCESS');
        }
        return view('centros.conciliadores.index', compact('conciliadores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('centros.conciliadores.create');
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
            'persona_id' => 'required|Integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        if($request->id != "" && $request->id != null){
            $conciliador = Conciliador::find($request->id);
            $conciliador->update(["persona_id" => $request->persona_id,"centro_id" => $request->centro_id]);
        }else{
            $req = $request->all();
            if (auth()->user()->hasRole("Super Usuario")) {
                $req["centro_id"] = $request->centro_id;
            } else {
                $req["centro_id"] = auth()->user()->centro_id;
            }
            $conciliador = Conciliador::create($req);
        }
        return $conciliador;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Conciliador::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $conciliador = Conciliador::find($id);
        return view('centros.conciliadores.edit')->with('conciliador', $conciliador);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conciliador $conciliador)
    {
        $validator = Validator::make($request->all(), [
            'centro_id' => 'required|Integer',
            'persona_id' => 'required|Integer',
            'rol_conciliador_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $conciliador->fill($request->all())->save();
        return $conciliador;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conciliador = Conciliador::findOrFail($id)->delete();
        return 204;
    }
    /**
     * Función para guardar modificar y eliminar disponibilidades
     * @param Request $request
     * @return Sala $sala
     */
    public function disponibilidad(Request $request){
        $conciliador = Conciliador::find($request->id);
        foreach($request->datos as $value){
            if(!$value["borrar"] || $value["borrar"] == 'false'){
                if($value["disponibilidad_id"] != ""){
                    $disponibilidad = Disponibilidad::find($value["disponibilidad_id"]);
                    $disponibilidad->update(['dia' => $value["dia"],'hora_inicio' => $value["hora_inicio"],'hora_fin' => $value["hora_fin"]]);
                }else{
                    $conciliador->disponibilidades()->create(['dia' => $value["dia"],'hora_inicio' => $value["hora_inicio"],'hora_fin' => $value["hora_fin"]]);
                }
            }else{
                $disponibilidad = Disponibilidad::find($value["disponibilidad_id"])->delete();
            }
        }
        return $conciliador;
    }
    
    /**
     * Funcion para guardar y modificar incidencias
     * @param Request $request
     * @return Sala $sala
     */
    public function incidencia(Request $request){
        $conciliador = Conciliador::find($request->id);
        if($request->incidencia_id == ""){
            $conciliador->incidencias()->create(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio." 00:00:00","fecha_fin" => $request->fecha_fin." 23:59:59"]);
        }else{
            $incidencia = Incidencia::find($request->incidencia_id);
            $incidencia->update(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio." 00:00:00","fecha_fin" => $request->fecha_fin." 23:59:59"]);
        }
        return $conciliador;
    }
    
    /**
     * Funcion para obtener el objeto centro con sus disponibilidades e incidencias
     * @param Request $request
     * @return Sala $sala
     */
    public function getDisponibilidades(Request $request){
        $conciliador = Conciliador::find($request->id);
        $conciliador->disponibilidades = $conciliador->disponibilidades;
        $conciliador->incidencias = $conciliador->incidencias;
        $conciliador->persona = $conciliador->persona;
        $conciliador->RolesConciliador = $conciliador->RolesConciliador;
        return $conciliador;
    }
    /**
     * Funcion guardar y eliminar los roles asignados a los conciliadores
     * @param Request $request
     * @return Sala $conciliador
     */
    public function roles(Request $request){
        $conciliador = Conciliador::find($request->id);
        if($request->rol_conciliador_id != "" && $request->borrar){            
            $roles = RolConciliador::find($request->rol_conciliador_id)->delete();
        }else{
            $conciliadorRol = RolConciliador::create(["conciliador_id" => $request->id, "rol_atencion_id" => $request->rol_atencion_id]);
        }
        return $conciliador;
    }
    /**
     * Funcion para obtener conciliadores disponibles
     * @param Request $request
     * @return Sala $conciliador
     */
    public function conciliadoresDisponibles(Request $request){
        $conciliadores = Conciliador::all();
        foreach ($conciliadores as $key => $value){
            $persona = Persona::find($value->id);
            $conciliadores[$key]["persona"] = $persona;
        }
        return $conciliadores;
    }
    
    public function conciliadorAudiencias(){
        return view('centros.conciliadores.agenda');
    }
}
