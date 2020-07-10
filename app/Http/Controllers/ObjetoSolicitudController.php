<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use Illuminate\Http\Request;
use Validator;
use App\ObjetoSolicitud;

class ObjetoSolicitudController extends Controller
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
      $objetoSolicitud = (new CatalogoFilter(ObjetoSolicitud::query(), $this->request))
          ->searchWith(ObjetoSolicitud::class)
          ->filter();

        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $objetoSolicitud->select(["id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $objetoSolicitud = $objetoSolicitud->withTrashed()->get();
            return $this->sendCSVResponse($objetoSolicitud->toArray(),['id','nombre','creado','modificado','eliminado'], 'CatalogoObjetoSolicitud.csv');
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos
      // de lo contrario paginamos
      if ($this->request->get('all')) {
          $objetoSolicitud = $objetoSolicitud->get();
      } else {
          $objetoSolicitud->select("id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado");
          $objetoSolicitud = $objetoSolicitud->paginate($this->request->get('per_page', 10));
      }

      if ($this->request->wantsJson()) {
          return $this->sendResponse($objetoSolicitud, 'SUCCESS');
      }
      return view('catalogos.objetoSolicitud.index', compact('objetoSolicitud'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.objetoSolicitud.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ObjetoSolicitud::create($request->all());
        return redirect('objeto-solicitud');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ObjetoSolicitud $objetoSolicitud)
    {
        return $objetoSolicitud;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $objetoSolicitud = ObjetoSolicitud::find($id);
        return view('catalogos.objetoSolicitud.edit')->with('objetoSolicitud', $objetoSolicitud);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $objetoSolicitud = ObjetoSolicitud::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100'
            ]
        );

          $objetoSolicitud->update($request->all());
          return redirect('objeto-solicitud')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjetoSolicitud $objetoSolicitud)
    {
        $objetoSolicitud->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($objetoSolicitud->id, 'SUCCESS');
        }

        return redirect()->route('objeto-solicitud.index')->with('success', 'Se ha eliminado exitosamente');
    }
}
