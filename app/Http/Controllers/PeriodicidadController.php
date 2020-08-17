<?php

namespace App\Http\Controllers;
use App\Periodicidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PeriodicidadController extends Controller
{
    protected $request;
    public function __construct(Request $request) {
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
        // $periodicidades = Periodicidad::all();
        $periodicidades = Periodicidad::paginate($this->request->get('per_page', 10));
        if ($this->request->wantsJson()) {
            return $this->sendResponse($periodicidades, 'SUCCESS');
        }
        return view('catalogos.periodicidades.index', compact('periodicidades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.periodicidades.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Cache::flush();
        Periodicidad::create($request->all());
        return redirect('periodicidades');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $periodicidad = Periodicidad::find($id);
        return view('catalogos.periodicidades.edit')->with('periodicidad', $periodicidad);
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
        Cache::flush();
        $periodicidad = Periodicidad::find($id);
        $periodicidad->update($request->all());
        return redirect('periodicidades');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $periodicidad = Periodicidad::find($id);
        $periodicidad->delete();
        return redirect('periodicidades');
    }
}
