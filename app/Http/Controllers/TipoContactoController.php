<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoContacto;
use App\Filters\CatalogoFilter;

class TipoContactoController extends Controller
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
        $tipoContacto = (new CatalogoFilter(TipoContacto::query(), $this->request))
            ->searchWith(TipoContacto::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $tipoContacto = $tipoContacto->get();
        } else {
            $tipoContacto->select("id","nombre","created_at as creado","updated_at as modificado","deleted_at as eliminado");
            $tipoContacto = $tipoContacto->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($tipoContacto, 'SUCCESS');
        }
        $tipos = $tipoContacto;
        return view('catalogos.tipos_contactos.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.tipos_contactos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        TipoContacto::create($request->all());
        return redirect('tipos_contactos');
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
        $tipo = TipoContacto::find($id);
        return view('catalogos.tipos_contactos.edit')->with('tipo', $tipo);
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
        $tipo = TipoContacto::find($id);
        $tipo->update($request->all());
        return redirect('tipos_contactos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipo = TipoContacto::find($id);
        $tipo->delete();
        return redirect('tipos_contactos');
    }
}
