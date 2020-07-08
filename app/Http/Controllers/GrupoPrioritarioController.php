<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\GrupoPrioritario;
use Validator;
use Illuminate\Http\Request;

class GrupoPrioritarioController extends Controller
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
        $grupoPrioritario = (new CatalogoFilter(GrupoPrioritario::query(), $this->request))
            ->searchWith(GrupoPrioritario::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $grupoPrioritario = $grupoPrioritario->get();
        } else {
            $grupoPrioritario = $grupoPrioritario->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($grupoPrioritario, 'SUCCESS');
        }
        return view('catalogos.grupoPrioritario.index', compact('grupoPrioritario'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.grupoPrioritario.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        GrupoPrioritario::create($request->all());
        return redirect('grupo-prioritario')->with('success', 'Se ha creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return GrupoPrioritario::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $grupoPrioritario = GrupoPrioritario::find($id);
        return view('catalogos.grupoPrioritario.edit')->with('grupoPrioritario', $grupoPrioritario);
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
        $grupoPrioritario = GrupoPrioritario::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100'
            ]
        );

        $grupoPrioritario->update($request->all());
        return redirect('grupo-prioritario')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        GrupoPrioritario::find($id)->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($id, 'SUCCESS');
        }

        return redirect()->route('grupo-prioritario.index')->with('success', 'Se ha eliminado exitosamente');
    }
}
