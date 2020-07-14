<?php

namespace App\Http\Controllers;

use App\TipoDiscapacidad;
use Illuminate\Http\Request;
use App\Filters\CatalogoFilter;

class TipoDiscapacidadController extends Controller
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
        $tipoDiscapacidades = (new CatalogoFilter(TipoDiscapacidad::query(), $this->request))
            ->searchWith(TipoDiscapacidad::class)
            ->filter();

        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $archivo_csv = 'CatalogoTipoDiscapacidad.csv';
            $query = $tipoDiscapacidades;
            $query->select(["id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $query = $query->withTrashed()->get();
            return $this->sendCSVResponse($query->toArray(),['id','nombre','creado','modificado','eliminado'], $archivo_csv);
        }


        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $tipoDiscapacidades = $tipoDiscapacidades->get();
        } else {
            $tipoDiscapacidades->select("id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $tipoDiscapacidades = $tipoDiscapacidades->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($tipoDiscapacidades, 'SUCCESS');
        }
        $tipos = $tipoDiscapacidades;
        return view('catalogos.tipos_discapacidades.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.tipos_discapacidades.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        TipoDiscapacidad::create($request->all());
        return redirect('tipos_discapacidades');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TipoDiscapacidad  $tipoDiscapacidad
     * @return \Illuminate\Http\Response
     */
    public function show(TipoDiscapacidad $tipoDiscapacidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TipoDiscapacidad  $tipoDiscapacidad
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipo = TipoDiscapacidad::find($id);
        return view('catalogos.tipos_discapacidades.edit')->with('tipo', $tipo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TipoDiscapacidad  $tipoDiscapacidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoDiscapacidad::find($id);
        $tipo->update($request->all());
        return redirect('tipos_discapacidades');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TipoDiscapacidad  $tipoDiscapacidad
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipo = TipoDiscapacidad::find($id);
        $tipo->delete();
        return redirect('tipos_discapacidades');
    }
}
