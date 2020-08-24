<?php

namespace App\Http\Controllers;

use App\Asentamiento;
use App\Filters\CatalogoFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsentamientoController extends Controller
{
    protected $request;
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
        //
    }

     /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function filtrarAsentamientos()
    {
        $asentamiento = (new CatalogoFilter(Asentamiento::query(), $this->request))
            ->searchWith(Asentamiento::class)
            ->filter(false);
            DB::enableQueryLog();
        $direccion = $this->request->get('direccion');
        $estado = $this->request->get('estado');
        $limite = 20;
        $minSimilitud = strlen($direccion) > 8 ? 0.6 : 0.3 ;
        // $giros_comerciales = GiroComercial::find(1)->descendants;
        // $asentamiento=$asentamiento->select("id","cp","asentamiento","tipo_asentamiento","municipio","estado",DB::raw("CONCAT(cp,',',asentamiento,',',tipo_asentamiento,',',municipio,',',estado) as direccion"))->orderBy('estado','asc')->get();
        $query = DB::table('asentamientos')
        ->select(DB::raw("id,asentamiento,municipio,estado,cp,strict_word_similarity(unaccent('{$direccion}'), unaccent(asentamiento)) as similarity"))
        ->whereRaw(DB::raw("strict_word_similarity(unaccent('{$direccion}'), unaccent(asentamiento)) between {$minSimilitud} and 1"));
        if($estado) {
            $query->whereRaw(DB::raw("lower(unaccent('{$estado}')) = lower(unaccent(estado))"));
        }
        $asentamiento = $query->orderByRaw(DB::raw("estado = 'CIUDAD DE MEXICO',similarity desc"))
        ->limit($limite)
        ->get();

        // $asentamiento = $asentamiento->testwhere('direccion','like',"%".$direccion."%");

        if($direccion != ""){
            $direccion = strtr($direccion,array('a'=> '(a|á)','e'=> '(e|é)','i'=>'(i|í)','o'=> '(o|ó)','u'=> '(u|ú)'));
        }


        if ($this->request->wantsJson()) {
            return $this->sendResponse($asentamiento, 'SUCCESS');
        }
        // else{
        //     return view('expediente.solicitudes.index');
        // }
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
