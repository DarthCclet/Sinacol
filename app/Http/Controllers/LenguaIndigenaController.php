<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LenguaIndigena;
use App\Filters\CatalogoFilter;

class LenguaIndigenaController extends Controller
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
        $lenguaIndigena = (new CatalogoFilter(LenguaIndigena::query(), $this->request))
            ->searchWith(LenguaIndigena::class)
            ->filter();

        //Evaluamos si es una consulta de la ruta de catálogos entonces regresamos CSV
        if ($this->request->is('catalogos/*')){
            $archivo_csv = 'CatalogoLenguaIndigena.csv';
            $query = $lenguaIndigena;
            $query->select(["id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado"]);
            $query = $query->withTrashed()->get();
            return $this->sendCSVResponse($query->toArray(),['id','nombre','creado','modificado','eliminado'], $archivo_csv);
        }

        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $lenguaIndigena = $lenguaIndigena->get();
        } else {
            $lenguaIndigena->select("id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $lenguaIndigena = $lenguaIndigena->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($lenguaIndigena, 'SUCCESS');
        }
        $lenguas = $lenguaIndigena;
        return view('catalogos.lenguas_indigenas.index', compact('lenguas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.lenguas_indigenas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        LenguaIndigena::create($request->all());
        return redirect('lenguas_indigenas');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(LenguaIndigena $lenguaIndigena)
    {
        return $lenguaIndigena;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lengua = LenguaIndigena::find($id);
        return view('catalogos.lenguas_indigenas.edit')->with('lengua', $lengua);
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
        $lengua = LenguaIndigena::find($id);
        $lengua->update($request->all());
        return redirect('lenguas_indigenas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lengua = LenguaIndigena::find($id);
        $lengua->delete();
        return redirect('lenguas_indigenas');
    }
}
