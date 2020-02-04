<?php

namespace App\Http\Controllers;

use App\Filters\CatalogoFilter;
use App\Resolucion;
use Validator;
use Illuminate\Http\Request;

class ResolucionController extends Controller
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
        $resolucionAudiencia = (new CatalogoFilter(Resolucion::query(), $this->request))
            ->searchWith(Resolucion::class)
            ->filter();
        // Si en el request viene el parametro all entonces regresamos todos los elementos de lo contrario paginamos
        if ($this->request->get('all')) {
            $resolucionAudiencia = $resolucionAudiencia->get();
        } else {
            $resolucionAudiencia = $resolucionAudiencia->paginate($this->request->get('per_page', 10));
        }

        if ($this->request->wantsJson()) {
            return $this->sendResponse($resolucionAudiencia, 'SUCCESS');
        }
        return view('catalogos.resolucion.index', compact('resolucionAudiencia'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.resolucion.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      Resolucion::create($request->all());
      return redirect('resolucion-audiencia')->with('success', 'Se ha creado exitosamente');

        // //Instanciamos la clase Resolucion
        // $resolucion = new Resolucion;
        // //Declaramos la resolucion con el dato enviado en el request
        // $resolucion->resolucion = $request->resolucion;
        // //Guardamos el cambio en nuestro modelo
        // $resolucion->save();
        // return response()->json($resolucion, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Resolucion::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $resolucion = Resolucion::find($id);
        return view('catalogos.resolucion.edit')->with('resolucion', $resolucion);
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
        $resolucion = Resolucion::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100'
            ]
        );

        $resolucion->update($request->all());
        return redirect('resolucion-audiencia')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Resolucion::find($id)->delete();
      if ($this->request->wantsJson()) {
          return $this->sendResponse($id, 'SUCCESS');
      }

      return redirect()->route('resolucion-audiencia.index')->with('success', 'Se ha eliminado exitosamente');
    }
}
