<?php

namespace App\Http\Controllers;
use App\MotivoArchivado;
use Illuminate\Http\Request;

class MotivoArchivadoController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $motivos = MotivoArchivado::all();
        $motivos = MotivoArchivado::paginate($this->request->get('per_page', 10));
        if ($this->request->wantsJson()) {
            return $this->sendResponse($motivos, 'SUCCESS');
        }
        return view('catalogos.motivos_archivado.index', compact('motivos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.motivos_archivado.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        MotivoArchivado::create($request->all());
        return redirect('motivos_archivado');
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
        $motivo = MotivoArchivado::find($id);
        return view('catalogos.motivos_archivado.edit')->with('motivo', $motivo);
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
        $motivo = MotivoArchivado::find($id);
        $motivo->update($request->all());
        return redirect('motivos_archivado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $motivo = MotivoArchivado::find($id);
        $motivo->delete();
        return redirect('motivos_archivado');
    }
}
