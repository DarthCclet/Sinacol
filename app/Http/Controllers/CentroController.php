<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Centro;
use App\Disponibilidad;
use App\Estado;
use App\Incidencia;
use App\Filters\CatalogoFilter;
use App\TipoAsentamiento;
use App\TipoVialidad;

class CentroController extends Controller
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
        // Filtramos los centros con los parametros que vengan en el request
        $centros = (new CatalogoFilter(Centro::query(), $this->request))
            ->searchWith(Centro::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $centros = $centros->get();
        } else {
            $centros->select("id","nombre","duracionAudiencia","abreviatura");
            $centros = $centros->paginate($this->request->get('per_page', 10));
        }

        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()) {
            
            return $this->sendResponse($centros, 'SUCCESS');
        }
        return view('centros.centros.index', compact('centros'));
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
        $estados = array_pluck(Estado::all(),'nombre','id');
        return view('centros.centros.create', compact('estados','tipos_asentamientos','tipos_vialidades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Centro::create($request->all());
        $centro = Centro::create($request->input('centro'));
        $domicilio = $request->input('domicilio');
        $centro->domicilios()->create($domicilio);
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
        $tipos_vialidades = array_pluck(TipoVialidad::all(),'nombre','id');
        $tipos_asentamientos = array_pluck(TipoAsentamiento::all(),'nombre','id');
        $estados = array_pluck(Estado::all(),'nombre','id');
        $centro->domicilios;
        return view('centros.centros.edit', compact('centro','estados','tipos_asentamientos','tipos_vialidades'));
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
        $centro->update($request->input('centro'));
        $domicilio = $request->input('domicilio');
        $centro->domicilios()->create($domicilio);
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
}
