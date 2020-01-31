<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\MotivoSolicitud;
use Validator;
use Illuminate\Http\Request;

class MotivoSolicitudController extends Controller
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
        $motivoSolicitud = (new CatalogoFilter(MotivoSolicitud::query(), $this->request))
            ->searchWith(MotivoSolicitud::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $motivoSolicitud = $motivoSolicitud->get();
        } else {
            $motivoSolicitud = $motivoSolicitud->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($motivoSolicitud, 'SUCCESS');
        }
        return view('catalogos.motivoSolicitud.index', compact('motivoSolicitud'));
        // return MotivoSolicitud::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.motivoSolicitud.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        MotivoSolicitud::create($request->all());
        return redirect('motivos-solicitud');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MotivoSolicitud $motivoSolicitud)
    {
        return $motivoSolicitud;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $motivoSolicitud = MotivoSolicitud::find($id);

        return view('catalogos.motivoSolicitud.edit')->with('motivoSolicitud', $motivoSolicitud);
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
        $motivoSolicitud = MotivoSolicitud::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100'
            ]
        );

          $motivoSolicitud->update($request->all());
// dd($motivoSolicitud);
          return redirect('motivos-solicitud')->with('success', 'Se ha actualizado exitosamente');

        // $motivoSolicitud->update($request->all());
        // return response()->json($motivoSolicitud, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MotivoSolicitud $motivoSolicitud)
    {

      // $motivoSolicitud = RolConciliador::findOrFail($id)->delete();
      $motivoSolicitud->delete();
      if ($this->request->wantsJson()) {
          return $this->sendResponse($motivoSolicitud->id, 'SUCCESS');
      }

      return redirect()->route('motivos-solicitud.index')->with('success', 'Se ha eliminado exitosamente');

    }
}
