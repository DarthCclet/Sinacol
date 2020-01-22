<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\compareciente;
use Validator;
class ComparecienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Compareciente::all();
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
            'audiencia_id' => 'required|Integer',
            'parte_id' => 'required|Integer',
            'presentado' => 'required|Boolean'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Compareciente::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Compareciente::find($id);
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
            'audiencia_id' => 'required|Integer',
            'parte_id' => 'required|Integer',
            'presentado' => 'required|Boolean'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $res = Compareciente::find($id);
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
        $compareciente = Compareciente::findOrFail($id)->delete();
        return 204;
    }
}
