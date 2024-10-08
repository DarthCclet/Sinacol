<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Disponibilidad;
use Validator;

class DisponibilidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Disponibilidad::all();
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
            'dia' => 'required|max:1',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'disponibilidad_id' => 'required|Integer',
            'disponibilidad_type' => 'required|max:30'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Disponibilidad::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Disponibilidad::find($id);
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
    public function update(Request $request,Disponibilidad $disponibilidad)
    {
        $validator = Validator::make($request->all(), [
            'dia' => 'required|max:1',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'disponibilidad_id' => 'required|Integer',
            'disponibilidad_type' => 'required|max:30'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $disponibilidad->fill($request->all())->save();
        return $disponibilidad;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $disponibilidad = Disponibilidad::findOrFail($id)->delete();
        return 204;
    }
}
