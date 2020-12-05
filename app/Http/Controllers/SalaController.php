<?php

namespace App\Http\Controllers;
use App\Filters\CatalogoFilter;
use App\Sala;
use App\Disponibilidad;
use App\Incidencia;
use Validator;
use Illuminate\Http\Request;

class SalaController extends Controller
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
        Sala::with('centro')->get();

        // Filtramos las salas con los parametros que vengan en el request
        $salas = (new CatalogoFilter(Sala::query(), $this->request))
            ->searchWith(Sala::class)
            ->filter();
        if(!auth()->user()->hasRole('Super Usuario')){
            $salas->where("centro_id",auth()->user()->centro_id);
        }
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $salas = $salas->get();
        } else {
            $salas = $salas->paginate($this->request->get('per_page', 10));
        }

        // Para cada objeto obtenido cargamos sus relaciones.
         $salas = tap($salas)->each(function ($sala) {
             $sala->loadDataFromRequest();
         });

        // Si el request solicita respuesta en JSON (es el caso de API y requests ajax)
        if ($this->request->wantsJson()) {
            return $this->sendResponse($salas, 'SUCCESS');
        }
        return view('centros.salas.index', compact('salas'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('centros.salas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id != "" && $request->id != null){
            $sala = Sala::find($request->id);
            $sala->update(["sala" => $request->sala,"centro_id" => $request->centro_id]);
        }else{
            $sala = Sala::create($request->all());
        }
        return $sala;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sala = Sala::find($id);
        return $sala;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $sala = Sala::find($id);

      return view('centros.salas.edit')->with('sala', $sala);
      // return view('centros.salas.edit', compact('sala'));

      // return view('centros.salas.edit')->withSala($sala);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sala $sala)
    {
        $validator = Validator::make($request->all(), [
            'sala' => 'required|max:100',
            'centro_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
          return Redirect::to('salas/' . $sala->id . '/edit')
                ->withErrors($validator);
            // return response()->json($validator, 201);
        }else{
          $sala->update($request->all());
          // $sala->fill($request->all())->save();
        }
        // return redirect()->back();

        return redirect('salas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sala = Sala::findOrFail($id)->delete();
        return redirect('salas');
        // return 204;
    }
    
    /**
     * Función para guardar modificar y eliminar disponibilidades
     * @param Request $request
     * @return Sala $sala
     */
    public function disponibilidad(Request $request){
        $sala = Sala::find($request->id);
        foreach($request->datos as $value){
            if(!$value["borrar"] || $value["borrar"] == 'false'){
                if($value["disponibilidad_id"] != ""){
                    $disponibilidad = Disponibilidad::find($value["disponibilidad_id"]);
                    $disponibilidad->update(['dia' => $value["dia"],'hora_inicio' => $value["hora_inicio"],'hora_fin' => $value["hora_fin"]]);
                }else{
                    $sala->disponibilidades()->create(['dia' => $value["dia"],'hora_inicio' => $value["hora_inicio"],'hora_fin' => $value["hora_fin"]]);
                }
            }else{
                $disponibilidad = Disponibilidad::find($value["disponibilidad_id"])->delete();
            }
        }
        return $sala;
    }
    
    /**
     * Funcion para guardar y modificar incidencias
     * @param Request $request
     * @return Sala $sala
     */
    public function incidencia(Request $request){
        $sala = Sala::find($request->id);
        if($request->incidencia_id == ""){
            $sala->incidencias()->create(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio,"fecha_fin" => $request->fecha_fin]);
        }else{
            $incidencia = Incidencia::find($request->incidencia_id);
            $incidencia->update(["justificacion" => $request->justificacion,"fecha_inicio" => $request->fecha_inicio,"fecha_fin" => $request->fecha_fin]);
        }
        return $sala;
    }
    
    /**
     * Funcion para obtener el objeto centro con sus disponibilidades e incidencias
     * @param Request $request
     * @return Sala $sala
     */
    public function getDisponibilidades(Request $request){
        $sala = Sala::find($request->id);
        $sala->disponibilidades = $sala->disponibilidades;
        $sala->incidencias = $sala->incidencias;
        return $sala;
    }
}
