<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parte;
use App\Filters\ParteFilter;

class ParteController extends Controller
{

    /**
     * Instancia del request
     * @var Request
     */
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
        $partes = Parte::all();
        
        // Filtramos los usuarios con los parametros que vengan en el request
        $partes = (new ParteFilter(Parte::query(), $this->request))
            ->searchWith(Parte::class)
            ->filter();

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $partes = $partes->get();
        } else {
            $partes = $partes->paginate($this->request->get('per_page', 10));
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $partes = tap($partes)->each(function ($partes) {
            $partes->loadDataFromRequest();
        });

        return $this->sendResponse($partes, 'SUCCESS');
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
            'solicitud_id' => 'required|Integer',
            'tipo_parte_id' => 'required|Integer',
            'genero_id' => 'required|Integer',
            'tipo_persona_id' => 'required|Integer',
            'nacionalidad_id' => 'required|Integer',
            'entidad_nacimiento_id' => 'required|Integer',
            'fecha_nacimiento' => 'required|Date',
            'nombre' => 'required|max:500|String',
            'primer_apellido' => 'required|max:500|String',
            'segundo_apellido' => 'required|max:500|String',
            'nombre_comercial' => 'required|max:500|String',
            'edad' => 'required|max:500|String',
            'rfc' => 'required|max:500|String',
            'curp' => 'required|max:500|String',
        ]);

        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
       $parte = Parte::create($request->all());

       return response()->json($parte, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Parte $parte)
    {
        return response()->json( ['partes' => $parte]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Parte $parte)
    {
        return $parte;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parte $parte)
    {
        $validator = Validator::make($request->all(), [
            'solicitud_id' => 'required|Integer',
            'tipo_parte_id' => 'required|Integer',
            'genero_id' => 'required|Integer',
            'tipo_persona_id' => 'required|Integer',
            'nacionalidad_id' => 'required|Integer',
            'entidad_nacimiento_id' => 'required|Integer',
            'fecha_nacimiento' => 'required|Date',
            'nombre' => 'required|max:500|String',
            'primer_apellido' => 'required|max:500|String',
            'segundo_apellido' => 'required|max:500|String',
            'nombre_comercial' => 'required|max:500|String',
            'edad' => 'required|max:500|String',
            'rfc' => 'required|max:500|String',
            'curp' => 'required|max:500|String',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
        $parte->update($request->all());
  
        return response()->json($parte, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parte $parte)
    {
        $parte->delete();
      return response()->json(null,204);
    }
}
