<?php

namespace App\Http\Controllers;
use App\ConceptoPagoResolucion;
use Illuminate\Http\Request;

class ConceptoPagoResolucionesController extends Controller
{
 // private $docu;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $conceptos = ConceptoPagoResolucion::all();
        $conceptos = ConceptoPagoResolucion::paginate($this->request->get('per_page', 10));
        if ($this->request->wantsJson()) {
            return $this->sendResponse($conceptos, 'SUCCESS');
        }
        return view('catalogos.conceptos_pagos.index', compact('conceptos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.conceptos_pagos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ConceptoPagoResolucion::create($request->all());
        return redirect('conceptos_pagos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ConceptoPagoResolucion $conceptos)
    {
        return $conceptos;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $concepto = ConceptoPagoResolucion::find($id);
        return view('catalogos.conceptos_pagos.edit')->with('concepto', $concepto);
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
        $concepto = ConceptoPagoResolucion::find($id);
        $concepto->update($request->all());
        return redirect('conceptos_pagos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $concepto = ConceptoPagoResolucion::find($id);
        $concepto->delete();
        return redirect('conceptos_pagos');
    }
}
