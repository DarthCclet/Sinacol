<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Incidencia;
use Validator;
class IncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Incidencia::all();
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
            'fecha_inicio' => 'required|Date',
            'fecha_fin' => 'required|Date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'incidenciable_id' => 'required|Integer',
            'incidenciable_type' => 'required|max:30'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Incidencia::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Incidencia::find($id);
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|Date',
            'fecha_fin' => 'required|Date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'incidenciable_id' => 'required|Integer',
            'incidenciable_type' => 'required|max:30'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $res = Incidencia::find($id);
        $res->update($request->all());
        return $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $incidencia = Incidencia::findOrFail($id)->delete();
        return 204;
    }
}
