<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use App\Audiencia;
use App\Solicitud;
use App\ClasificacionArchivo;
use App\Parte;
use Exception;
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
            $directorio = 'expedientes/'.$audiencia->expediente_id.'/audiencias/'.$request->audiencia_id[0];
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
    public function solicitud(Request $request)
    {
        
       if(!isset($request->parte) || $request->parte[0] == null || !isset($request->tipo_documento_id) || $request->tipo_documento_id[0] == null){
            return '{ "files": [ { "error": "No se capturó tipo de documento o parte solicitada", "name": "thumb2.jpg" } ] }';
       }

        $parte = Parte::find($request->parte[0]);
        $solicitud = Solicitud::find($request->solicitud_id[0]);
        
        try{
            $existeDocumento = $parte->documentos;
            if($solicitud != null && count($existeDocumento) == 0 ){
                $archivo = $request->files;
                $solicitud_id = $solicitud->id;
                $clasificacion_archivo= $request->tipo_documento_id[0];
                $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                Storage::makeDirectory($directorio);
                $archivos = $request->file('files');
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                foreach($archivos as $archivo) {
                    $path = $archivo->store($directorio);

                    $documento = $parte->documentos()->create([
                        "nombre" => str_replace($directorio."/", '',$path),
                        "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                        "ruta" => $path,
                        "tipo_almacen" => "local",
                        "uri" => $path,
                        "longitud" => round(Storage::size($path) / 1024, 2),
                        "firmado" => "false",
                        "clasificacion_archivo_id" => $tipoArchivo->id ,
                    ]);
                }
                return '{ "files": [ { "success": "Documento almacenado correctamente", "error":0, "name": "'.$tipoArchivo->nombre.'.pdf" } ] }';
            }else{
                return '{ "files": [ { "error": "Ya existe un documento para este solicitante", "name": "" } ] }';

            }
            
        }catch(Exception $e){

            return '{ "files": [ { "error": "No se pudo guardar el archivo", "name": "thumb2.jpg" } ] }';
        }
        return '{ "files": [ { "error": "No se capturó solicitud", "name": "thumb2.jpg" } ] }';
    }
    public function postComparece(Request $request)
    {
        
       if(!isset($request->parte) || $request->parte[0] == null || !isset($request->tipo_documento_id) || $request->tipo_documento_id[0] == null){
            return '{ "files": [ { "error": "No se capturo tipo de documento o parte solicitada", "name": "thumb2.jpg" } ] }';
       }
        $parte = Parte::find($request->parte[0]);
        $audiencia = Audiencia::find($request->audiencia_idC[0]);
        
        try{
            $existeDocumento = $parte->documentos;
            if($audiencia != null && count($existeDocumento) == 0){
                $archivo = $request->files;
                $audiencia_id = $audiencia->id;
                $clasificacion_archivo= $request->tipo_documento_id[0];
                $directorio = 'expedientes/'.$audiencia->expediente_id.'/audiencias/'.$audiencia_id;
                Storage::makeDirectory($directorio);
                $archivos = $request->file('files');
                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
                foreach($archivos as $archivo) {
                    $path = $archivo->store($directorio);

                    $documento = $parte->documentos()->create([
                        "nombre" => str_replace($directorio."/", '',$path),
                        "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                        "ruta" => $path,
                        "tipo_almacen" => "local",
                        "uri" => $path,
                        "longitud" => round(Storage::size($path) / 1024, 2),
                        "firmado" => "false",
                        "clasificacion_archivo_id" => $tipoArchivo->id ,
                    ]);
                }
                return '{ "files": [ { "success": "Documento almacenado correctamente", "error":0, "name": "'.$tipoArchivo->nombre.'.pdf" } ] }';
            }else{
                return '{ "files": [ { "error": "Ya existe un documento para este solicitante", "name": "" } ] }';

            }
            
        }catch(Exception $e){

            return '{ "files": [ { "error": "No se pudo guardar el archivo", "name": "thumb2.jpg" } ] }';
        }
        return '{ "files": [ { "error": "No se capturó solicitud", "name": "thumb2.jpg" } ] }';
    }
    public function getFile($id){
        $documento = Documento::find($id);
        $file = Storage::get($documento->ruta);
        $fileMime = Storage::mimeType($documento->ruta);
        return response($file, 200)->header('Content-Type', $fileMime);
    }
    
}
