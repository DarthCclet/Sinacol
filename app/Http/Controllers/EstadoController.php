<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Estado;
use App\Filters\CatalogoFilter;

class EstadoController extends Controller
{
    protected $request;

 // private $docu;
    public function __construct(Request $request)
    {
        $this->request = $request;
        // $this->docu = new ComunicacionCJF();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estados = (new CatalogoFilter(Estado::query(), $this->request))
            ->searchWith(Estado::class)
            ->filter();
        
        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $archivo_csv = 'CatalogoEstados.csv';
            $query = $estados;
            $query->select(["id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $query = $query->withTrashed()->get();
            return $this->sendCSVResponse($query->toArray(),['id','nombre','creado','modificado','eliminado'], $archivo_csv);
        }
        
        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $estados = $estados->get();
        } else {
            $estados->select("id","nombre","abreviatura","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $estados = $estados->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($estados, 'SUCCESS');
        }
        return view('catalogos.estados.index', compact('estados'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.estados.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Estado::create($request->all());
        return redirect('estados');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Estado $estado)
    {
        return $estado;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $estado = Estado::find($id);
        return view('catalogos.clasificacion_archivo.edit')->with('estado', $estado);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estado $estado)
    {
        $estado->update($request->all());
        return redirect('estados');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estado $estado)
    {
        $estado->delete();
        return response()->json(null,204);
    }
}
