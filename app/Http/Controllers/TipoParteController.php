<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoParte;
use App\Filters\CatalogoFilter;

class TipoParteController extends Controller
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
        $tipoPartes = (new CatalogoFilter(TipoParte::query(), $this->request))
            ->searchWith(TipoParte::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $tipoPartes = $tipoPartes->get();
        } else {
            $tipoPartes->select("id","nombre");
            $tipoPartes = $tipoPartes->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($tipoPartes, 'SUCCESS');
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
