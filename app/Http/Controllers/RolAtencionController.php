<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\RolAtencion;
use Validator;
use Illuminate\Http\Request;

class RolAtencionController extends Controller
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
        $rolesAtencion = (new CatalogoFilter(RolAtencion::query(), $this->request))
            ->searchWith(RolAtencion::class)
            ->filter();

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $rolesAtencion = $rolesAtencion->get();
        } else {
            $rolesAtencion = $rolesAtencion->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($rolesAtencion, 'SUCCESS');
        }
        return view('admin.rolesAtencion.index', compact('rolesAtencion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.rolesAtencion.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        RolAtencion::create($request->all());
      return redirect('roles-atencion');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(RolAtencion $rolAtencion)
    {
        return $rolAtencion;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $rolAtencion = RolAtencion::find($id);
      return view('admin.rolesAtencion.edit')->with('rolAtencion', $rolAtencion);
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
      $rolAtencion = RolAtencion::find($id);
      $request->validate(
          [
            'nombre' => 'required|max:100'
          ]
      );

        $rolAtencion->update($request->all());

        return redirect('roles-atencion')->with('success', 'Se ha actualizado el rol exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id )
    {

        $rolAtencion = RolAtencion::findOrFail($id)->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($id, 'SUCCESS');
        }

        return redirect()->route('roles-atencion.index')->with('success', 'Se ha eliminado el rol exitosamente');
    }
}
