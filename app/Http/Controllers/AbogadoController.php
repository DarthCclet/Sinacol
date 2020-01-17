<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Abogado;

class AbogadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Abogado::all();
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
            'nombre' => 'required|max:500|String',
            'primer_apellido' => 'required|max:500|String',
            'segundo_apellido' => 'required|max:500|String',
            'cedula_profesional' => 'required|max:500|String',
            'numero_empleado' => 'required|max:500|String',
            'email' => 'required|max:500|String',
            'profedet' => 'required|Boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
       $solicitud = Solicitud::create($request->all());

       return response()->json($solicitud, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Abogado $abogado)
    {
        return response()->json( ['abogados' => $abogado]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Abogado $abogado)
    {
        return $abogado;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Abogado $abogado)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:500|String',
            'primer_apellido' => 'required|max:500|String',
            'segundo_apellido' => 'required|max:500|String',
            'cedula_profesional' => 'required|max:500|String',
            'numero_empleado' => 'required|max:500|String',
            'email' => 'required|max:500|String',
            'profedet' => 'required|Boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
        $abogado->update($request->all());
  
        return response()->json($abogado, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Abogado $abogado)
    {
        $abogado->delete();
        return response()->json(null,204);
    }
}
