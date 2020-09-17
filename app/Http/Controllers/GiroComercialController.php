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
        if(!$request->is('externo/*')){
            $this->middleware('auth');
        }
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

        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $archivo_csv = 'CatalogoGirosComerciales.csv';
            $query = $giroComercial;
            $query->select(["id","nombre","codigo","_lft","_rgt","parent_id","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $query = $query->withTrashed()->get();
            return $this->sendCSVResponse($query->toArray(),['id','nombre',"codigo","_lft","_rgt","parent_id",'creado','modificado','eliminado'], $archivo_csv);
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $giroComercial = $giroComercial->with('ambito')->get();
        } else {
            $giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $giroComercial = $giroComercial->with('ambito')->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($giroComercial, 'SUCCESS');
        }

        $giros = GiroComercial::with('ambito')->defaultOrder()->withDepth()->get();
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
        $giro = GiroComercial::find($id);
        return $giro;
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
        $giro = GiroComercial::find($id);
        $giro->update(["nombre" => $request->nombre]);
        return $giro;
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
     * @return \Illuminate\Http\Response
     */
    public function filtrarGirosComerciales()
    {
        $giroComercial = (new CatalogoFilter(GiroComercial::query(), $this->request))
            ->searchWith(GiroComercial::class)
            ->filter(false);
        $nombre = $this->request->get('nombre');

        // $giros_comerciales = GiroComercial::find(1)->descendants;
        if($nombre != ""){
            $nombre = strtr($nombre,array('a'=> '(a|á)','e'=> '(e|é)','i'=>'(i|í)','o'=> '(o|ó)','u'=> '(u|ú)'));
            $giroComercial=$giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id","ambito_id")->where('nombre','~*',$nombre)->with('ancestors')->withDepth()->orderBy('codigo','asc')->get();
        }else{
            $giroComercial=$giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id","ambito_id")->withDepth()->orderBy('codigo','asc')->get();
        }


        if ($this->request->wantsJson()) {
            return $this->sendResponse($giroComercial, 'SUCCESS');
        }
        // else{
        //     return view('expediente.solicitudes.index');
        // }
    }

    public function CambiarAmbito(Request $request){
        $giro = GiroComercial::find($request->id);
        $ambitoActual = $request->ambito_id;
        if($ambitoActual == 2 || $ambitoActual == 3){
            $ambitoNuevo = 1;
        }else{
            $ambitoNuevo = 2;
        }
        $giro->update(["ambito_id" => $ambitoNuevo]);
        $arreglo []= ["id" => $giro->id , "ambito_id" => $giro->ambito_id,"nombre" => $giro->ambito->nombre];
        $arreglo = $this->CambiarAmbitoChildrens($giro,$arreglo);
        $arreglo = $this->CambiarAmbitoParents($giro,$arreglo);
        return $arreglo;
    }

    private function CambiarAmbitoParents($giro,$arreglo){
        #validamos si tiene padre
        if($giro->parent_id != "" && $giro->parent_id != null){
            # obtenemos el padre
            $padre = GiroComercial::find($giro->parent_id);
            #obtenemos a todos los hijos
            $hijos = GiroComercial::where("parent_id",$padre->id)->get();
            #declaramos bandera para validar a los hijos
            #Si un hijo es diferente al nuevo ambito del giro, automaticamente cambia a mixto
            #Si todas son iguales colocamos el nuevo ambito tambien al padre
            $bandera = true;

            #Recorremos los hijos
            foreach($hijos as $hijo){
                if($hijo->ambito_id != $giro->ambito_id){
                    $bandera=false;
                }
            }
            if($bandera){
                $padre->update(["ambito_id" => $giro->ambito_id]);
            }else{
                $padre->update(["ambito_id" => 3]);
            }
            #Agregamos el padre al arreglo de la respuesta
            $arreglo []= ["id" => $padre->id , "ambito_id" => $padre->ambito_id,"nombre" => $padre->ambito->nombre];
            #Llamamos a la misma función para verificar que no tenga padre el padre y si lo tiene modificarlo
            $arreglo = $this->CambiarAmbitoParents($padre,$arreglo);
            return $arreglo;
        }else{
            return $arreglo;
        }
    }
    private function CambiarAmbitoChildrens($giro,$arreglo){
        //buscamos los hijos
        $hijos = GiroComercial::where("parent_id",$giro->id)->get();
        // Recorremos los hijos
        foreach($hijos as $hijo){
            #Modificamos el ambito del hijo
            $hijo->update(["ambito_id" => $giro->ambito_id]);
            #Agregamos el hijo al arreglo de la respuesta
            $arreglo []= ["id" => $hijo->id , "ambito_id" => $hijo->ambito_id,"nombre" => $hijo->ambito->nombre];
            #Llamamos a la misma función para verificar que no tenga hijos el giro y si los tiene modificarlos
            $arreglo = $this->CambiarAmbitoChildrens($hijo,$arreglo);
        }
        return $arreglo;
    }
    public function CambiarPadre(Request $request){
        ##Buscamos el giro
        $giro = GiroComercial::find($request->mover_id);
        $giroNuevoPadre = GiroComercial::find($request->a_id);
        if($giroNuevoPadre->parent_id == $request->mover_id){
            return array("status" => "error","mensaje" => "No puedes asignar al giro padre como hijo de uno de sus hijos");
        }
        ##modificamos el padre
        $giro->update(["parent_id" => $request->a_id]);
        $giro->status = "success";
        ##regresamos el nuevo giro
        return $giro;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGirosComercialesByNivelId(Request $request)
    {
        $giroComercial = (new CatalogoFilter(GiroComercial::query(), $this->request))
            ->searchWith(GiroComercial::class)
            ->filter(false);
        $nivel = $this->request->get('nivel');
        $id = $this->request->get('id');
        $muestraCodigo = $this->request->get('muestraCodigo');

        // $giros_comerciales = GiroComercial::find(1)->descendants;
        if($nivel != ""){
            $orden = "nombre";
            if($id != ""){
                $giroComercial->whereDescendantOf($id);
            }
            if($muestraCodigo){
                $orden = "codigo";
            }
            // dd(DB::getQueryLog());
            $giroComercial = $giroComercial->withDepth()->with('ambito')->orderBy($orden,"asc")->get();
            $giroComercial = $giroComercial->where("depth",$nivel);
        }else{
            $giroComercial=$giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id")->with('ambito')->withDepth()->get();
        }


        if ($this->request->wantsJson()) {
            return $this->sendResponse($giroComercial, 'SUCCESS');
        }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAncestors(Request $request)
    {
        $giroComercial = (new CatalogoFilter(GiroComercial::query(), $this->request))
            ->searchWith(GiroComercial::class)
            ->filter();
            $request->validate([
                'id' => 'required',
            ]);
        $id = $this->request->get('id');
        if($id != ""){
            $giroComercial = $giroComercial->find($id)->ancestors()->withDepth()->orderBy('depth','asc')->get();
        }else{
            $giroComercial=$giroComercial->select("id","nombre","codigo","_lft","_rgt","parent_id")->with('ambito')->withDepth()->get();
        }


        if ($this->request->wantsJson()) {
            return $this->sendResponse($giroComercial, 'SUCCESS');
        }

    }
}
