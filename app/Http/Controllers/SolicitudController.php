<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Solicitud;
use Validator;
use App\Filters\SolicitudFilter;

class SolicitudController extends Controller
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
        $solicitud = Solicitud::all();
        
        // Filtramos los usuarios con los parametros que vengan en el request
        $solicitud = (new SolicitudFilter(Solicitud::query(), $this->request))
            ->searchWith(Solicitud::class)
            ->filter();

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $solicitud = $solicitud->get();
        } else {
            $solicitud = $solicitud->paginate($this->request->get('per_page', 10));
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $solicitud = tap($solicitud)->each(function ($solicitud) {
            $solicitud->loadDataFromRequest();
        });

        return $this->sendResponse($solicitud, 'SUCCESS');
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
            'ratificada' => 'Boolean',
            'fecha_ratificacion' => 'required|Date',
            'fecha_recepcion' => 'required|Date',
            'fecha_conflicto' => 'required|Date',
            'observaciones' => 'required|max:500',
            'presenta_abogado' => 'required',
            'abogado_id' => 'required|Integer',
            'estatus_solicitud_id' => 'required|Integer',
            'motivo_solicitud_id' => 'required|Integer',
            'centro_id' => 'required|Integer',
            'user_id' => 'required|Integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
       $solicitud = Solicitud::create($request->all());

       return response()->json($solicitud, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function show(Solicitud $solicitud)
    {
        return response()->json( ['solicitudes' => $solicitud]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function edit(Solicitud $solicitud)
    {
        return $solicitud;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitud $solicitud)
    {
      $validator = Validator::make($request->all(), [
          'ratificada' => 'Boolean|required',
          'fecha_ratificacion' => 'required|Date',
          'fecha_recepcion' => 'required|Date',
          'fecha_conflicto' => 'required|Date',
          'observaciones' => 'required|max:500',
          'presenta_abogado' => 'required|Boolean',
          'abogado_id' => 'required|Integer',
          'estatus_solicitud_id' => 'required|Integer',
          'motivo_solicitud_id' => 'required|Integer',
          'centro_id' => 'required|Integer',
          'user_id' => 'required|Integer',
      ]);
      if ($validator->fails()) {
          return response()->json($validator, 201);
                      // ->withInput();
      }
      $solicitud->update($request->all());

      return response()->json($solicitud, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function destroy(Solicitud $solicitud)
    {
      $solicitud->delete();
      return response()->json(null,204);
    }
}
