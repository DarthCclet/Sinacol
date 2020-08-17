<?php

namespace App\Http\Controllers;

use App\TipoContador;
use Illuminate\Http\Request;

class TipoContadorController extends Controller
{
    protected $request;
    public function __construct(Request $request) {
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
        // $tipos = TipoContador::all();
        $tipos = TipoContador::paginate($this->request->get('per_page', 10));
        if ($this->request->wantsJson()) {
            return $this->sendResponse($tipos, 'SUCCESS');
        }
        return view('catalogos.tipos_contadores.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.tipos_contadores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        TipoContador::create($request->all());
        return redirect('tipos_contadores');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TipoContador  $tipoContador
     * @return \Illuminate\Http\Response
     */
    public function show(TipoContador $tipoContador)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TipoContador  $tipoContador
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipo = TipoContador::find($id);
        return view('catalogos.tipos_contadores.edit')->with('tipo', $tipo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TipoContador  $tipoContador
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoContador::find($id);
        $tipo->update($request->all());
        return redirect('tipos_contadores');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TipoContador  $tipoContador
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipo = TipoContador::find($id);
        $tipo->delete();
        return redirect('tipos_contadores');
    }
}
