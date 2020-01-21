<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Audiencia;
use Validator;
class AudienciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Audiencia::all();
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
            'expediente_id' => 'required|Integer',
            'conciliador_id' => 'required|Integer',
            'sala_id' => 'required|Integer',
            'resolucion_id' => 'required|Integer',
            'parte_responsable_id' => 'required|Integer',
            'fecha_audiencia' => 'required|Date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_audiencia' => 'required|Integer',
            'reprogramada' => 'required|Boolean',
            'desahogo' => 'required|max:3000',
            'convenio' => 'required|max:3000'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Audiencia::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Audiencia::find($id);
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
            'expediente_id' => 'required|Integer',
            'conciliador_id' => 'required|Integer',
            'sala_id' => 'required|Integer',
            'resolucion_id' => 'required|Integer',
            'parte_responsable_id' => 'required|Integer',
            'fecha_audiencia' => 'required|Date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'numero_audiencia' => 'required|Integer',
            'reprogramada' => 'required|Boolean',
            'desahogo' => 'required|max:3000',
            'convenio' => 'required|max:3000'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $res = Audiencia::find($id);
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
        $audiencia = Audiencia::findOrFail($id)->delete();
        return 204;
    }
}
