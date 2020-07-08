<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\EstatusSolicitud;
use Validator;
use Illuminate\Http\Request;

class EstatusSolicitudController extends Controller
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
        $estatusSolicitud = (new CatalogoFilter(EstatusSolicitud::query(), $this->request))
            ->searchWith(EstatusSolicitud::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $estatusSolicitud = $estatusSolicitud->get();
        } else {
            $estatusSolicitud = $estatusSolicitud->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($estatusSolicitud, 'SUCCESS');
        }
        return view('catalogos.estatusSolicitud.index', compact('estatusSolicitud'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.estatusSolicitud.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        EstatusSolicitud::create($request->all());
        return redirect('estatus-solicitud');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(EstatusSolicitud $estatusSolicitud)
    {
        return $estatusSolicitud;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $estatusSolicitud = EstatusSolicitud::find($id);
        return view('catalogos.estatusSolicitud.edit')->with('estatusSolicitud', $estatusSolicitud);
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
        $estatusSolicitud = EstatusSolicitud::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100'
            ]
        );

          $estatusSolicitud->update($request->all());
          return redirect('estatus-solicitud')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EstatusSolicitud $estatusSolicitud)
    {
        $estatusSolicitud->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($estatusSolicitud->id, 'SUCCESS');
        }

        return redirect()->route('estatus-solicitud.index')->with('success', 'Se ha eliminado exitosamente');
    }
}
