<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use App\Audiencia;
use App\Solicitud;
use App\ClasificacionArchivo;
use App\Conciliador;
use App\Events\GenerateDocumentResolution;
use App\FirmaDocumento;
use App\Parte;
use App\Traits\GenerateDocument;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;
use Illuminate\Support\Str;

class DocumentoController extends Controller
{
    use GenerateDocument;
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
        try{

            $audiencia = Audiencia::find($request->audiencia_id[0]);
            if($audiencia != null){
                $directorio = 'expedientes/'.$audiencia->expediente_id.'/audiencias/'.$request->audiencia_id[0];
                Storage::makeDirectory($directorio);
                $archivos = $request->file('files');
                $tipoArchivo = ClasificacionArchivo::find($request->tipo_documento_id[0]);
                foreach($archivos as $archivo) {
                    $path = $archivo->store($directorio);
                    $uuid = Str::uuid();
                    $audiencia->documentos()->create([
                        "nombre" => str_replace($directorio."/", '',$path),
                        "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                        "ruta" => $path,
                        "uuid" => $uuid,
                        "tipo_almacen" => "local",
                        "uri" => $path,
                        "longitud" => round(Storage::size($path) / 1024, 2),
                        "firmado" => "false",
                        "clasificacion_archivo_id" => $tipoArchivo->id ,
                    ]);
                }
            }
            return 'Product saved successfully';
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return 'Product saved successfully';

        }
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
                    $uuid = Str::uuid();
                    $documento = $parte->documentos()->create([
                        "nombre" => str_replace($directorio."/", '',$path),
                        "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                        "ruta" => $path,
                        "uuid" => $uuid,
                        "tipo_almacen" => "local",
                        "uri" => $path,
                        "longitud" => round(Storage::size($path) / 1024, 2),
                        "firmado" => "false",
                        "clasificacion_archivo_id" => $tipoArchivo->id ,
                    ]);
                }
                return '{ "files": [ { "success": "Documento almacenado correctamente","thumbnailUrl":"/documentos/getFile/'.$documento->uuid.'" ,"error":0, "name": "'.$tipoArchivo->nombre.'.pdf" } ] }';
            }else{
                return '{ "files": [ { "error": "Ya existe un documento para este solicitante", "name": "" } ] }';

            }
            
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
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
                    $uuid = Str::uuid();
                    $documento = $parte->documentos()->create([
                        "nombre" => str_replace($directorio."/", '',$path),
                        "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                        "descripcion" => "Documento de audiencia ".$tipoArchivo->nombre,
                        "ruta" => $path,
                        "uuid" => $uuid,
                        "tipo_almacen" => "local",
                        "uri" => $path,
                        "longitud" => round(Storage::size($path) / 1024, 2),
                        "firmado" => "false",
                        "clasificacion_archivo_id" => $tipoArchivo->id ,
                    ]);
                }
                return '{ "files": [ { "success": "Documento almacenado correctamente","thumbnailUrl":"/documentos/getFile/'.$documento->uuid.'" ,"error":0, "name": "'.$tipoArchivo->nombre.'.pdf" } ] }';
            }else{
                return '{ "files": [ { "error": "Ya existe un documento para este solicitante", "name": "" } ] }';

            }
            
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return '{ "files": [ { "error": "No se pudo guardar el archivo", "name": "thumb2.jpg" } ] }';
        }
        return '{ "files": [ { "error": "No se capturó solicitud", "name": "thumb2.jpg" } ] }';
    }
    public function getFile($uuid){
        try{

            $documento = Documento::where('uuid',$uuid)->first();
            if($documento){
                $file = Storage::get($documento->ruta);
                $fileMime = Storage::mimeType($documento->ruta);
                return response($file, 200)->header('Content-Type', $fileMime);
            }else{
                abort(404);
            }
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            abort(404);
        }
    }
    public function aviso_privacidad(){
        try{
            $file = Storage::get("aviso_privacidad/aviso-privacidad.pdf");
            $fileMime = Storage::mimeType("aviso_privacidad/aviso-privacidad.pdf");
            return response($file, 200)->header('Content-Type', $fileMime);
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            abort(404);
        }
    }

    public function preview(Request $request)
    {
        try {
            $idSolicitud = $request->get('solicitud_id');
            $idAudiencia = $request->get('audiencia_id');
            $plantilla_id = $request->get('plantilla_id', 1);
            $pdf = $request->exists('pdf');

            $solicitud = Solicitud::find($idSolicitud);
            if ($solicitud) {
                if (!$idAudiencia && isset($solicitud->expediente->audiencia->first()->id)) {
                    $idAudiencia = $solicitud->expediente->audiencia->first()->id;
                }
            }

            $html = $this->renderDocumento(
                $idAudiencia,
                $idSolicitud,
                $plantilla_id,
                "",//solicitante
                "",//solicitado
                "",//documento
            );

            if($pdf) {
                return $this->renderPDF($html, $plantilla_id);
            }
            else{
                echo $html; exit;
            }
        } catch (\Throwable $th) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            //throw $th;
        }
    }

    public function firmado(Request $request)
    {
        try {
            $idParte = $request->get('parte_id');
            $tipoPersona = $request->get('tipo_persona');
            $idSolicitud = $request->get('solicitud_id');
            $idAudiencia = $request->get('audiencia_id');
            $idPlantilla = $request->get('plantilla_id');
            $idDocumento = $request->get('documento_id');
            $idSolicitado = $request->get('solicitado_id');
            $idSolicitante = $request->get('solicitante_id');
            $firmaBase64 = $request->get('img_firma');

            if($tipoPersona!='conciliador'){
                $model = 'Parte';
            }else{
                $model = 'Conciliador';
            }
            //guardar o actualizar firma
            $firmaDocumento = FirmaDocumento::where('firmable_id',$idParte)->where('plantilla_id',$idPlantilla)->where('audiencia_id',$idAudiencia)->first();
            if($firmaDocumento != null){
                $firmaDocumento->update([
                    "firma" => $firmaBase64
                ]);
            }else{
                $firmaDocumento = FirmaDocumento::create([
                    "firmable_type" => $model,
                    "firmable_id" => $idParte,
                    "audiencia_id" => $idAudiencia,
                    "solicitud_id" => $idSolicitud,
                    "plantilla_id" => $idPlantilla,
                    "firma" => $firmaBase64
                ]);
            }
            //eliminar documento con codigo QR
            $documento = Documento::find($idDocumento);
            $clasificacionArchivo = $documento->clasificacion_archivo_id;
            $totalFirmantes = $documento->total_firmantes;
            $firmasDocumento = FirmaDocumento::where('plantilla_id',$idPlantilla)->where('audiencia_id',$idAudiencia)->get();
            if($totalFirmantes == count($firmasDocumento)){
                if($documento != null){
                    $documento->delete();
                }
                //generar documento con firmas
                event(new GenerateDocumentResolution($idAudiencia,$idSolicitud,$clasificacionArchivo,$idPlantilla,$idSolicitante,$idSolicitado));
            }


            return response()->json([
                'success' => true,
                'message' => 'OK',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'ERROR:'.$e,
            ], 200);
        }
    }

    public function storeDocument(Request $request){
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required',
            'nombre_documento' => 'required',
            'solicitud_id' => 'integer|required',               
            'fileDocumento' => 'max:10000|mimes:pdf',               
        ],[
            'fileDocumento.max' => 'El documento no puede ser de tamaño mayor a :max Kb.',
            'descripcion.required' => 'El campo :required es requerido.',
            'nombre_documento.required' => 'El campo :required es requerido.',
            'solicitud_id.required' => 'El campo :required es requerido.',
        ]);
        if ($validator->fails()) {
            $error = "";
            foreach ($validator->errors()->all() as $key => $value) {
                $error .= " - ".$value;   
            }
            return response()->json(['success' => false, 'message' => 'Por favor verifica tus datos: '.$error, 'data' => null], 200);
        }
        try{
            $solicitud = Solicitud::find($request->solicitud_id );
            $archivo = $request->fileDocumento;
            $clasificacion_archivo= 37;
            $directorio = '/solicitud/' . $solicitud->id;
            Storage::makeDirectory($directorio);
            $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);
            
            $path = $archivo->store($directorio);
            if($solicitud && $archivo ){
                $uuid = Str::uuid();
                $documento = $solicitud->documentos()->create([
                    "nombre" => $request->nombre_documento,
                    "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                    // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                    "descripcion" => $request->descripcion,
                    "ruta" => $path,
                    "uuid" => $uuid,
                    "tipo_almacen" => "local",
                    "uri" => $path,
                    "longitud" => round(Storage::size($path) / 1024, 2),
                    "firmado" => "false",
                    "clasificacion_archivo_id" => $tipoArchivo->id ,
                ]);
                if($documento != null){
                    return response()->json(['success' => true, 'message' => 'Se guardo correctamente', 'data' => $documento], 200);
                }
            }
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'No se pudo guardar el documento', 'data' => null], 200);
        }
    }

    public function generar_documento(){
        
        try{
            
            $clasificacion_archivos = array_pluck(ClasificacionArchivo::whereIn('id',[13,14,15,16,17,18,40])->orderBy('nombre')->get(),'nombre','id');
            return view('herramientas.regenerar_documentos', compact('clasificacion_archivos'));
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
                       return view('herramientas.regenerar_documentos');
        }
    }
    
    public function storeRegenerarDocumento(Request $request){
        
        try{
            $arrayPlantilla = [40=>6,18=>7,17=>1,16=>2,15=>3,14=>4,13=>10];
            $idSolicitud = $request->get('solicitud_id',1);
            $idAudiencia = $request->get('audiencia_id');
            $clasificacion_archivo_id = $request->get('clasificacion_archivo_id');
            $idSolicitante = $request->get('solicitante_id');
            $idSolicitado = $request->get('solicitado_id');
            $plantilla_id = $arrayPlantilla[$clasificacion_archivo_id];
            event(new GenerateDocumentResolution($idAudiencia, $idSolicitud, $clasificacion_archivo_id, $plantilla_id, $idSolicitante, $idSolicitado));
            return response()->json(['success' => true, 'message' => 'Se genero el documento correctamente', 'data' => null], 200);
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
            " Se emitió el siguiente mensaje: ". $e->getMessage().
            " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'No se genero el documento correctamente', 'data' => null], 200);
            
        }
    }
    
}
