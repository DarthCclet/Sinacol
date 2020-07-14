<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClasificacionArchivo;
use App\Filters\CatalogoFilter;

class ClasificacionArchivoController extends Controller
{
    protected $request;

 // private $docu;
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
        $clasificacion = (new CatalogoFilter(ClasificacionArchivo::query(), $this->request))
            ->searchWith(ClasificacionArchivo::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $clasificacion = $clasificacion->get();
        } else {
            $clasificacion->select("id","nombre","entidad","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $clasificacion = $clasificacion->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($clasificacion, 'SUCCESS');
        }
        return view('catalogos.clasificacion_archivo.index', compact('clasificacion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.clasificacion_archivo.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ClasificacionArchivo::create($request->all());
        return redirect('clasificacion_archivos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ClasificacionArchivo $clasificacion)
    {
        return $clasificacion;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clasificacion = ClasificacionArchivo::find($id);
        return view('catalogos.clasificacion_archivo.edit')->with('clasificacion', $clasificacion);
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
        $clasificacion = ClasificacionArchivo::find($id);
        $clasificacion->update($request->all());
        return redirect('clasificacion_archivos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $clasificacion = ClasificacionArchivo::find($id);
        $clasificacion->delete();
        return redirect('clasificacion_archivos');
    }
}
