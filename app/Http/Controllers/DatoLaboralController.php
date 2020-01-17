<?php

namespace App\Http\Controllers;

use App\DatoLaboral;
use Illuminate\Http\Request;

class DatoLaboralController extends Controller
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
        //
        return DatoLaboral::all();
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
        //
         return DatoLaboral::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DatoLaboral $datoLaboral)
    {
        //
        return  $datoLaboral;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DatoLaboral $datoLaboral)

    {
        //
        // $datoLaboral->fill($request->all())->save();
        $datoLaboral->update($request->all());

        return response()->json($datoLaboral, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DatoLaboral $datoLaboral)
    {
        $datoLaboral->delete();
        return response()->json(null,204);
    }
}
