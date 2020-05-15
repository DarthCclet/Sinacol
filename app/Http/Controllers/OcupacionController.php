<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\Ocupacion;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\ComunicacionCJF;


class OcupacionController extends Controller
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
        $ocupacion = (new CatalogoFilter(Ocupacion::query(), $this->request))
            ->searchWith(Ocupacion::class)
            ->filter();

        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $archivo_csv = 'CatalogoOcupacion.csv';
            $query = $ocupacion;
            $query->select(["id","nombre","salario_zona_libre","salario_resto_del_pais","vigencia_de","vigencia_a","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $query = $query->withTrashed()->get();
            return $this->sendCSVResponse($query->toArray(),['id','nombre',"salario_zona_libre","salario_resto_del_pais","vigencia_de","vigencia_a",'creado','modificado','eliminado'], $archivo_csv);
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $ocupacion = $ocupacion->get();
        } else {
            $ocupacion->select("id","nombre","salario_zona_libre","salario_resto_del_pais","vigencia_de","vigencia_a","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $ocupacion = $ocupacion->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($ocupacion, 'SUCCESS');
        }
        return view('catalogos.ocupaciones.index', compact('ocupacion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      // $this->docu->enviaDocumentoCJF();
        return view('catalogos.ocupaciones.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // dd($request);
      $request->validate(
          [
              'nombre' => 'required|alpha|max:100',
              'salario_zona_libre' => 'required',
              'salario_resto_del_pais' => 'required',
              'vigencia_de' => 'required'
          ]
      );
        Ocupacion::create($request->all());
        return redirect('ocupaciones')->with('success', 'Se ha creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Ocupacion::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ocupacion = Ocupacion::find($id);
        return view('catalogos.ocupaciones.edit')->with('ocupacion', $ocupacion);
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
        $ocupacion = Ocupacion::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100',
              'salario_zona_libre' => 'required',
              'salario_resto_del_pais' => 'required',
              'vigencia_de' => 'required'
            ]
        );

        $ocupacion->update($request->all());
        return redirect('ocupaciones')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Ocupacion::find($id)->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($id, 'SUCCESS');
        }

        return redirect()->route('ocupaciones.index')->with('success', 'Se ha eliminado exitosamente');
    }

    // public function formatoFecha($fecha){
    //   return "2020-02-10";
    //   // dd($fecha);
    // }
    /**
     * Función para guardar y modificar multiples ocupaciones
     * @param Request $request
     * @return Ocupacion $ocupacion
     */
    public function editMultiple(Request $request)
    {
      DB::beginTransaction();
        if( (isset($request->salario_zona_libre) && $request->salario_zona_libre != null)  || (isset($request->salario_resto_del_pais) && $request->salario_resto_del_pais != null) ){
          try{
            foreach($request->ids as $idOcupacion){
                $ocupacion = Ocupacion::find($idOcupacion);
                $ocupacion->update(['vigencia_a' => date('d/m/Y') ]);
                $newOcupacion = Ocupacion::create(['nombre' => $ocupacion->nombre, 'vigencia_de' => $request->vigencia_de, 'vigencia_a' => $request->vigencia_a, 'salario_zona_libre'=>$request->salario_zona_libre, 'salario_resto_del_pais'=>$request->salario_resto_del_pais ]);
                $ocupacion->delete();
            }
            DB::commit();
          }catch (\Throwable $e) {
            DB::rollback();
            throw $e;
          }
        }else{
          try{
            foreach($request->ids as $idOcupacion){
              $ocupacion = Ocupacion::find($idOcupacion);
              $ocupacion->update(['vigencia_de' => $request->vigencia_de, 'vigencia_a_' => $request->vigencia_a ]);
            }
            DB::commit();
          }catch (\Throwable $e) {
            DB::rollback();
            throw $e;
          }
        }
      return $ocupacion;
      // return redirect()->route('ocupaciones.index')->with('success', 'Se han actualizado exitosamente');
        // return view('catalogos.ocupaciones.edit')->with('ocupacion', $ocupacion);
    }
}
