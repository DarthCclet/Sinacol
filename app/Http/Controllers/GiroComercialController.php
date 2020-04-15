<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GiroComercial;
use App\Filters\CatalogoFilter;

class GiroComercialController extends Controller
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
        $giroComercial = (new CatalogoFilter(GiroComercial::query(), $this->request))
            ->searchWith(GiroComercial::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $giroComercial = $giroComercial->get();
        } else {
            $giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id");
            $giroComercial = $giroComercial->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($giroComercial, 'SUCCESS');
        }

        $giros = GiroComercial::defaultOrder()->withDepth()->get();
        return view('admin.giros.index', compact('giros'));
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function filtrarGirosComerciales()
    {
        $giroComercial = (new CatalogoFilter(GiroComercial::query(), $this->request))
            ->searchWith(GiroComercial::class)
            ->filter();
        $nombre = $this->request->get('nombre');
        
        // $giros_comerciales = GiroComercial::find(1)->descendants;
        if($nombre != ""){
            $giroComercial=$giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id")->where('nombre','like',"%".$nombre."%")->with('ancestors')->withDepth()->get();
        }else{
            $giroComercial=$giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id")->withDepth()->get();
        }
        
        
        if ($this->request->wantsJson()) {
            return $this->sendResponse($giroComercial, 'SUCCESS');
        }
        // else{
        //     return view('expediente.solicitudes.index');
        // }
    }
}
