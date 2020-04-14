<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Filters\CatalogoFilter;
use Validator;
use App\TipoDocumento;

class TipoDocumentoController extends Controller
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
      $tipoDocumento = (new CatalogoFilter(TipoDocumento::query(), $this->request))
          ->searchWith(TipoDocumento::class)
          ->filter();
      // Si en el request viene el parametro all entonces regresamos todos los elementos
      // de lo contrario paginamos
      if ($this->request->get('all')) {
          $tipoDocumento = $tipoDocumento->get();
      } else {
          $tipoDocumento->select('id','nombre');
          $tipoDocumento = $tipoDocumento->paginate($this->request->get('per_page', 10));
      }

      if ($this->request->wantsJson()) {
          return $this->sendResponse($tipoDocumento, 'SUCCESS');
      }
      return view('documentos.tiposDocumentos.index', compact('tipoDocumento'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $objetoDocumento = [];
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/elemento_documentos.json"));
        //Se llena el catalogo desde el arvhivo json elemento_documentos.json
        foreach ($json->datos as $key => $value){
            $objetoDocumento [] =
                [
                    'objeto' => $value->id,
                    'nombre' => $value->nombre,
                    'checked'=> ""
                ];
        }
        // dd($objetoDocumento);
        return view('documentos.tiposDocumentos.create',compact('objetoDocumento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $objetos = implode(",", array_unique($request->input('objetoD')) );
        $datosTipoD['nombre'] = $request->input('nombre');
        $datosTipoD['objetos'] = $objetos;
        TipoDocumento::create($datosTipoD);
        return redirect('tipo-documento')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TipoDocumento $tipoDocumento)
    {
        return $tipoDocumento;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipoDocumento = TipoDocumento::find($id);
        $obj = $tipoDocumento->getAttributes()['objetos'];
        $objetoDocumento = [];
        $path = base_path('database/datafiles');
        $json = json_decode(file_get_contents($path . "/elemento_documentos.json"));
        //Se llena el catalogo desde el arvhivo json elemento_documentos.json
        foreach ($json->datos as $key => $value){
          // dd($value);
            $check = ( strpos($obj,$value->id) !== false )? "checked": "" ;
            $objetoDocumento [] =
                [
                    'objeto' => $value->id,
                    'nombre' => $value->nombre,
                    'checked'=> $check
                ];
        }

        return view('documentos.tiposDocumentos.edit',compact('tipoDocumento','objetoDocumento'));
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
        $tipoDocumento = TipoDocumento::find($id);
        $request->validate(
            [
              'nombre' => 'required|max:100'
            ]
        );

        $objetos = implode(",", array_unique($request->input('objetoD')) );
        // dd($objetos);
        $datosTipoD['nombre'] = $request->input('nombre');
        $datosTipoD['objetos'] = $objetos;

        $tipoDocumento->update($datosTipoD);
        // $tipoDocumento->update($request->all());
        return redirect('tipo-documento')->with('success', 'Se ha actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoDocumento $tipoDocumento)
    {
        $tipoDocumento->delete();
        if ($this->request->wantsJson()) {
            return $this->sendResponse($tipoDocumento->id, 'SUCCESS');
        }

        return redirect()->route('tipo-documento.index')->with('success', 'Se ha eliminado exitosamente');
    }
}
