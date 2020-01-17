<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expediente;
use Validator;

class ExpedienteController extends Controller
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
        $expediente = Expediente::all();
        return $expediente;
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
        $validator = Validator::make($request->all(), [
            'folio' => 'required|max:10',
            'anio' => 'required|Integer',
            'consecutivo' => 'required|Integer',
            'solicitud_id' => 'required|Integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
       $expediente = Expediente::create($request->all());

       return response()->json($expediente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Expediente $expediente
     * @return \Illuminate\Http\Response
     */
    public function show(Expediente $expediente)
    {
        //
        return response()->json( ['expedientes' => $expediente]);
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
     * @param  Expediente $expediente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expediente $expediente)
    {
        //
        $validator = Validator::make($request->all(), [
            'folio' => 'required|max:10',
            'anio' => 'required|Integer',
            'consecutivo' => 'required|Integer',
            'solicitud_id' => 'required|Integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
                        // ->withInput();
        }
        $expediente->update($request->all());

        return response()->json($expediente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Expediente  $expediente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expediente $expediente)
    {
        //
        $expediente->delete();
        return response()->json(null,204);
    }
}
