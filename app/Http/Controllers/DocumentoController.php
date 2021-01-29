<?php

namespace App\Http\Controllers;

use App\Exceptions\CredencialesParaFirmaNoValidosException;
use App\Exceptions\TextoFirmableInexistenteException;
use EdgarOrozco\Docsigner\Facades\Docsigner;
use Goutte\Client;
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
use Symfony\Component\DomCrawler\Crawler;

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
            $path = public_path('/assets/img/asesoria/aviso-privacidad.pdf');
            return response()->file($path);
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
            $idSolicitado = $request->get('solicitado_id');
            $idSolicitante = $request->get('solicitante_id');

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
                $idSolicitante,//solicitante
                $idSolicitado,//solicitado
                ""//documento
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
            $idParte = $request->get('persona_id');
            $tipoPersona = $request->get('tipo_persona');
            $idSolicitud = $request->get('solicitud_id');
            $idAudiencia = $request->get('audiencia_id');
            $idPlantilla = $request->get('plantilla_id');
            $idDocumento = $request->get('documento_id');
            $idSolicitado = $request->get('solicitado_id');
            $idSolicitante = $request->get('solicitante_id');
            $firma_documento_id = $request->get('firma_documento_id');

            $firmaBase64 = $request->get('img_firma');
            $tipo_firma = $request->get('tipo_firma');
            $firma = null;

            //Si firma con llave publica, FIEL o FIREL o certificado X.509
            if($tipo_firma == 'llave-publica' || $tipo_firma == null) {
                list($firma, $texto_firmado) = $this->firmaConLlavePublica(
                    $request,
                    $idAudiencia,
                    $idSolicitud,
                    $idPlantilla
                );
            }
            //Si firma de forma autógrafa
            else if($tipo_firma == 'autografa') {
                $firma = $firmaBase64;
            }

            // if ($tipoPersona != 'conciliador') {
            //     $model = 'App\Parte';
            // } else {
            //     $model = 'App\Conciliador';
            // }

            //guardar o actualizar firma
            // $match = [
            //     'firmable_id'=>$idParte,
            //     'plantilla_id'=>$idPlantilla,
            //     'audiencia_id'=>$idAudiencia,
            // ];
            $firmaDocumento = FirmaDocumento::find($firma_documento_id);
            $firmaDocumento->update([
                "audiencia_id" => $idAudiencia,
                "solicitud_id" => $idSolicitud,
                "plantilla_id" => $idPlantilla,
                'tipo_firma' => $tipo_firma,
                'texto_firmado' => $texto_firmado,
                "firma" => $firma
            ]);
            // $updateOrCreate = ;
            // $firmaDocumento = FirmaDocumento::UpdateOrCreate($match, $updateOrCreate);

            //eliminar documento con codigo QR
            $documento = Documento::find($idDocumento);
            $documento->update(['firmado'=>true]);
            $clasificacionArchivo = $documento->clasificacion_archivo_id;
            $totalFirmantes = $documento->total_firmantes;
            $firmasDocumento = FirmaDocumento::where('plantilla_id', $idPlantilla)->where(
                'audiencia_id',
                $idAudiencia
            )->where('documento_id',$idDocumento)->whereRaw('firma is not null')->get();
            if ($totalFirmantes == count($firmasDocumento)) {
                // if ($documento != null) {
                //     $documento->delete();
                // }
                //generar documento con firmas
                event(
                    new GenerateDocumentResolution(
                        $idAudiencia,
                        $idSolicitud,
                        $clasificacionArchivo,
                        $idPlantilla,
                        $idSolicitante,
                        $idSolicitado,
                        $documento->id
                    )
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'OK',
            ], 200);

        } catch (\Throwable $e) {

            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensaje: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'ERROR:'.$e->getMessage(),
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

    /**
     * Recibe los archivos de llave, certificados y parámetros para firmado del documento y regresa un arreglo
     * con la firma en el primer elemento y en el segundo el texto que se firma.
     * @param Request $request
     * @param $idAudiencia
     * @param $idSolicitud
     * @param $idPlantilla
     * @return array
     * @throws CredencialesParaFirmaNoValidosException
     */
    protected function firmaConLlavePublica(Request $request, $idAudiencia, $idSolicitud, $idPlantilla)
    {
        $encoding_firmas = $request->get('encoding_firmas');
        $base_firmas_path = 'firmas';
        if(!Storage::exists($base_firmas_path)){
            Storage::makeDirectory($base_firmas_path);
        }
        if($encoding_firmas=='post') {
            $key_path = storage_path('app/' . $request->file('key')->store('firmas'));
            $cert_path = storage_path('app/' . $request->file('cert')->store('firmas'));
        }
        else{
            $binkey = file_get_contents($request->get('key'));
            $bincert = file_get_contents($request->get('cert'));
            $nombrekey = md5($request->get('key'));
            $nombrecert = md5($request->get('cert'));
            $key_path = storage_path('app/firmas/'.$nombrekey.'.key');
            $cert_path = storage_path('app/firmas/'.$nombrecert.'.cer');
            file_put_contents($key_path, $binkey);
            file_put_contents($cert_path, $bincert);
        }
        $password = $request->get('password');

        try {
            $texto_a_firmar = $this->textoQueSeFirma($idAudiencia, $idSolicitud, $idPlantilla);
            $firma = Docsigner::setCredenciales($cert_path, $key_path, $password)->firma($texto_a_firmar);
            return [$firma, $texto_a_firmar];
        } catch (\Exception $e) {
            $message = "No ha sido posible realizar la firma del documento. Favor de revisar la validéz de su clave, archivo .key y/o archivo.cer";
            throw new CredencialesParaFirmaNoValidosException($message);
        } finally {
            if(Storage::exists($key_path)) {
                Storage::delete($key_path);
            }
            if(Storage::exists($cert_path)) {
                Storage::delete($cert_path);
            }
        }

    }

    /**
     * @param $tipoPersona
     * @param $idParte
     * @param $idPlantilla
     * @param $idAudiencia
     * @param $firmaBase64
     * @param $idSolicitud
     * @param $idDocumento
     * @param $idSolicitante
     * @param $idSolicitado
     */
    protected function firmaConAutografaDigital(
        $tipoPersona,
        $idParte,
        $idPlantilla,
        $idAudiencia,
        $firmaBase64,
        $idSolicitud,
        $idDocumento,
        $idSolicitante,
        $idSolicitado
    ) {
    }

    /**
     * Devuelve el texto que se debe firmar, sin elementos html ni de control de plantilla, esto se extrae
     * del cuerpo del documento.
     *
     * @important Debe existir en la plantilla un tag con class = body si no existe emitirá una excepción
     *
     * @param $idAudiencia
     * @param $idSolicitud
     * @param $idPlantilla
     * @return string
     * @throws TextoFirmableInexistenteException
     */
    protected function textoQueSeFirma($idAudiencia, $idSolicitud, $idPlantilla): string
    {
        $html = $this->renderDocumento(
            $idAudiencia,
            $idSolicitud,
            $idPlantilla,
            "",//solicitante
            "",//solicitado
            ""//documento
        );

        //Ojo que aquí el supuesto es que en el documento hay un div o un tag que encierra todo el texto
        //útil a firmar. Sin encabezados ni elementos de control, sólo el texto firmable
        //Si no hay ese elemento este código va a emitir una excepción.

        $crawler = new Crawler($html);
        $elements = $crawler->filter('.body')->each(
            function ($node) {
                return $node->text();
            }
        );
        if(!$elements || !isset($elements[0])){
            throw new TextoFirmableInexistenteException("ELEMENTO CON CLASE .body NO SE ENCONTRÓ EN PLANTILLA", '20201');
        }
        $text_a_firmar = strip_tags($elements[0]);
        return $text_a_firmar;
    }

}
