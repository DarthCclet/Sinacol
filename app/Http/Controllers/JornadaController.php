<?php

namespace App\Http\Controllers;

use App\Jornada;
use Illuminate\Http\Request;

class JornadaController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jornadas = Jornada::all();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($jornadas, 'SUCCESS');
        }
        return view('catalogos.jornadas.index', compact('jornadas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.jornadas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        Jornada::create($request->all());
        return redirect('jornadas');
    }

    /**
     * Display the specified resource.
     *
     * @param  Jornada  $jornada
     * @return \Illuminate\Http\Response
     */
    public function show(Jornada $jornada)
    {
        return $jornada;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jornada = Jornada::find($id);
        return view('catalogos.jornadas.edit')->with('jornada', $jornada);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Jornada  $jornada
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Jornada $jornada)
    {
        $jornada->update($request->all());
        return redirect('jornadas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Jornada  $jornada
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jornada $jornada)
    {
        $jornada->delete();
        return response()->json(null,204);
    }
}
