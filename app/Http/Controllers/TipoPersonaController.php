<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoPersona;
use App\Filters\CatalogoFilter;

class TipoPersonaController extends Controller
{
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
        $tiposPersona = (new CatalogoFilter(TipoPersona::query(), $this->request))
            ->searchWith(TipoPersona::class)
            ->filter();

        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $tiposPersona->select(["id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $tiposPersona = $tiposPersona->withTrashed()->get();
            return $this->sendCSVResponse($tiposPersona->toArray(),['id','nombre','creado','modificado','eliminado'], 'CatalogoTipoPersona.csv');
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $tiposPersona = $tiposPersona->get();
        }
        else {
            $tiposPersona->select("id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $tiposPersona = $tiposPersona->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($tiposPersona, 'SUCCESS');
        }
        abort(404);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
