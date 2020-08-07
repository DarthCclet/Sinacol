<?php

namespace App\Http\Controllers;

use App\Contador;
use Illuminate\Http\Request;
use App\Filters\CatalogoFilter;

class ContadorController extends Controller
{
    protected $request;

    public function __construct(Request $request = null)
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
        Contador::with('tipoContador')->get();

        // Filtramos los usuarios con los parametros que vengan en el request
        $contadores = (new CatalogoFilter(Contador::query(), $this->request))
            ->searchWith(Contador::class)
            ->filter();
//        dd($contadores);
         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $contadores = $contadores->get();
        } else {
            $contadores = $contadores->paginate($this->request->get('per_page', 10));
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $contadores = tap($contadores)->each(function ($contador) {
            $contador->loadDataFromRequest();
        });

        // return $this->sendResponse($solicitud, 'SUCCESS');

        if ($this->request->wantsJson()) {
            return $this->sendResponse($contadores, 'SUCCESS');
        }
        return view('contadores.index', compact('contadores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contadores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id == ''){
            $contador = Contador::create($request->all());
        }else{
            $contador = Contador::find($request->id);
            $contador->update(["contador"=>$request->contador,"anio" => $request->anio,"centro_id" => $request->centro_id,"tipo_contador_id" => $request->tipo_contador_id]);
//            dd(Contador::find($request->id));
        }
        return $contador;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contador  $contador
     * @return \Illuminate\Http\Response
     */
    public function show(Contador $contador)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contador  $contador
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contador = Contador::find($id);
        return view('contadores.edit', compact('contador'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contador  $contador
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contador $contador)
    {
//        dd($request);
        $contador->update($request->all());
          // $sala->fill($request->all())->save();
//         return redirect()->back();
        return redirect('salas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contador  $contador
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contador $contador)
    {
        //
    }
    
    public function getContador($tipo_contador_id, $centro_id){
        $anio=date("Y");
        $contador = Contador::where("anio","=",$anio)->where("tipo_contador_id","=",$tipo_contador_id)->where("centro_id","=",$centro_id)->first();
        if($contador != null){
            Contador::find($contador->id)->update(["contador" => (int)$contador->contador + 1]);
        }else{
            $contador = Contador::create([
                "anio" => $anio,
                "tipo_contador_id" => $tipo_contador_id,
                "centro_id" => $centro_id,
                "contador" => 2,
            ]);
            $contador->contador = 1;
        }
        unset($contador->centro);
        return $contador;
    }
}
