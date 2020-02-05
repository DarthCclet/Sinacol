<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Filters\CatalogoFilter;
use App\Conciliador;
use App\Disponibilidad;
use App\Incidencia;
use Validator;
class ConciliadorController extends Controller
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
        Conciliador::with('persona','rolConciliador')->get();

        // Filtramos las salas con los parametros que vengan en el request
        $conciliadores = (new CatalogoFilter(Conciliador::query(), $this->request))
            ->searchWith(Conciliador::class)
            ->filter();

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $conciliadores = $conciliadores->get();
        } else {
            $conciliadores = $conciliadores->paginate($this->request->get('per_page', 10));
        }

        // Para cada objeto obtenido cargamos sus relaciones.
         $conciliadores = tap($conciliadores)->each(function ($conciliador) {
             $conciliador->loadDataFromRequest();
         });

        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()) {
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
            'centro_id' => 'required|Integer',
            'persona_id' => 'required|Integer',
            'rol_conciliador_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        if($request->id != "" && $request->id != null){
            $conciliador = Conciliador::find($request->id);
            $conciliador->update(["persona_id" => $request->persona_id,"centro_id" => $request->centro_id,"rol_conciliador_id" => $request->rol_conciliador_id]);
        }else{
            $conciliador = Conciliador::create($request->all());
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
            $conciliador->incidencias()->create(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio,"fecha_fin" => $request->fecha_fin]);
        }else{
            $incidencia = Incidencia::find($request->incidencia_id);
            $incidencia->update(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio,"fecha_fin" => $request->fecha_fin]);
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
        return $conciliador;
    }
}
