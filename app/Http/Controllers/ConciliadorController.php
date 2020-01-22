<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conciliador;
use Validator;
class ConciliadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Conciliador::all();
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
            'centro_id' => 'required|Integer',
            'persona_id' => 'required|Integer',
            'rol_conciliador_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Conciliador::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Conciliador::find($id);
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
    public function update(Request $request, Conciliador $conciliador)
    {
        $validator = Validator::make($request->all(), [
            'centro_id' => 'required|Integer',
            'persona_id' => 'required|Integer',
            'rol_conciliador_id' => 'required|Integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $conciliador->fill($request->all())->save();
        return $conciliador;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conciliador = Conciliador::findOrFail($id)->delete();
        return 204;
    }
}
