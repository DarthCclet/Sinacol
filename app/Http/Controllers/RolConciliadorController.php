<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\RolConciliador;
use Validator;
use Illuminate\Http\Request;

class RolConciliadorController extends Controller
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
        $rolesConciliadores = (new CatalogoFilter(RolConciliador::query(), $this->request))
            ->searchWith(RolConciliador::class)
            ->filter();

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $rolesConciliadores = $rolesConciliadores->get();
        } else {
            $rolesConciliadores = $rolesConciliadores->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($rolesConciliadores, 'SUCCESS');
        }
        return view('admin.rolesConciliadores.index', compact('rolesConciliadores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.rolesConciliadores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      RolConciliador::create($request->all());
      return redirect('roles-conciliadores');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(RolConciliador $rolConciliador)
    {
        return $rolConciliador;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $rolConciliador = RolConciliador::find($id);
      return view('admin.rolesConciliadores.edit')->with('rolConciliador', $rolConciliador);
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
      $rolConciliador = RolConciliador::find($id);
      $request->validate(
          [
            'nombre' => 'required|max:100'
          ]
      );

        $rolConciliador->update($request->all());

        return redirect('roles-conciliadores')->with('success', 'Se ha actualizado el rol exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id )
    {

        $rolConciliador = RolConciliador::findOrFail($id)->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($id, 'SUCCESS');
        }

        return redirect()->route('roles-conciliadores.index')->with('success', 'Se ha eliminado el rol exitosamente');
    }
}
