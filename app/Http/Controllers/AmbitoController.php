<?php

namespace App\Http\Controllers;

use App\Ambito;
use Illuminate\Http\Request;

class AmbitoController extends Controller
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
        // $ambitos = Ambito::all();
        $ambitos = Ambito::paginate($this->request->get('per_page', 10));
        if ($this->request->wantsJson()) {
            return $this->sendResponse($ambitos, 'SUCCESS');
        }
        return view('catalogos.ambitos.index', compact('ambitos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.ambitos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Ambito::create($request->all());
        return redirect('ambitos');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ambito  $ambito
     * @return \Illuminate\Http\Response
     */
    public function show(Ambito $ambito)
    {
        return $ambito;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ambito  $ambito
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ambito = Ambito::find($id);
        return view('catalogos.ambitos.edit')->with('ambito', $ambito);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ambito  $ambito
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ambito $ambito)
    {
        $ambito->update($request->all());
        return redirect('ambitos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ambito  $ambito
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ambito $ambito)
    {
        $ambito->delete();
        return redirect('ambitos');
    }
}
