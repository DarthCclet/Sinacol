<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;
use Validator;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Persona::all();
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
            'nombre' => 'required|max:50',
            'primer_apellido' => 'required|max:50',
            'segundo_apellido' => 'max:50',
            'razon_social' => 'required|max:100',
            'curp' => '',
            'rfc' => 'required|max:13',
            'fecha_nacimiento' => 'required|Date',
            'tipo_persona_id' => 'required|Integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Persona::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Persona::find($id);
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
    public function update(Request $request, Persona $persona)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
            'primer_apellido' => 'required|max:50',
            'segundo_apellido' => 'max:50',
            'razon_social' => 'required|max:100',
            'curp' => '',
            'rfc' => 'required|max:13',
            'fecha_nacimiento' => 'required|Date',
            'tipo_persona_id' => 'required|Integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $persona->fill($request->all())->save();
        return $persona;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $persona = Persona::findOrFail($id)->delete();
        return 204;
    }
}
