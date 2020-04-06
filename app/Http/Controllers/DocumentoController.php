<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use App\Audiencia;
use App\ClasificacionArchivo;
use Validator;
use Illuminate\Support\Facades\Storage;
class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Documento::all();
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
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|max:500',
            'ruta' => 'required|max:100',
            'documentable_id' => 'required|Integer',
            'documentable_type' => 'required|max:30'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        return Documento::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Documento::find($id);
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
    public function update(Request $request, Documento $documento)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|max:500',
            'ruta' => 'required|max:100',
            'documentable_id' => 'required|Integer',
            'documentable_type' => 'required|max:30'
        ]);
        if ($validator->fails()) {
            return response()->json($validator, 201);
        }
        $documento->fill($request->all())->save();
        return $documento;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id)->delete();
        return 204;
    }
    
    public function uploadSubmit(){
        
    }
    public function postAudiencia(Request $request)
    {
        $audiencia = Audiencia::find($request->audiencia_id[0]);
        if($audiencia != null){
            $directorio = 'audiencias/'.$request->audiencia_id[0];
            Storage::makeDirectory($directorio);
            $archivos = $request->file('files');
            $tipoArchivo = ClasificacionArchivo::find($request->tipo_documento_id[0]);
            foreach($archivos as $archivo) {
                $path = $archivo->store($directorio);
                $audiencia->documentos()->create([
                    "nombre" => str_replace($directorio."/", '',$path),
                    "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                    "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                    "ruta" => $path,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => $tipoArchivo->id ,
                ]);
            }
        }
        return 'Product saved successfully';
    }
    public function getFile($id){
        $documento = Documento::find($id);
        $file = Storage::get($documento->ruta);
        $fileMime = Storage::mimeType($documento->ruta);
        return response($file, 200)->header('Content-Type', $fileMime);
    }
    
}
