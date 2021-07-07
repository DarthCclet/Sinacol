<?php

namespace App\Http\Controllers;

use App\Audiencia;
use Illuminate\Http\Request;
use App\Parte;
use App\Contacto;
use App\TipoContacto;
use App\AudienciaParte;
use App\ClasificacionArchivo;
use App\DatoLaboral;
use App\Domicilio;
use App\Events\GenerateDocumentResolution;
use App\Filters\ParteFilter;
use App\Solicitud;
use Carbon\Carbon;
use Exception;
use App\BitacoraBuzon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Str;

class ParteController extends Controller
{

    /**
     * Instancia del request
     * @var Request
     */
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
        $partes = Parte::all();

        // Filtramos los usuarios con los parametros que vengan en el request
        $partes = (new ParteFilter(Parte::query(), $this->request))
            ->searchWith(Parte::class)
            ->filter();

         // Si en el request viene el parametro all entonces regresamos todos los elementos
        // de lo contrario paginamos
        if ($this->request->get('all')) {
            $partes = $partes->get();
        } else {
            $partes = $partes->paginate($this->request->get('per_page', 10));
        }

        // // Para cada objeto obtenido cargamos sus relaciones.
        $partes = tap($partes)->each(function ($partes) {
            $partes->loadDataFromRequest();
        });

        return $this->sendResponse($partes, 'SUCCESS');
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
            'solicitud_id' => 'required|Integer',
            'tipo_parte_id' => 'required|Integer',
            'genero_id' => 'nullable|Integer',
            'tipo_persona_id' => 'required|Integer',
            'nacionalidad_id' => 'nullable|Integer',
            'entidad_nacimiento_id' => 'nullable|Integer',
            'fecha_nacimiento' => 'nullable|Date',
            'nombre' => 'nullable|max:500|String',
            'primer_apellido' => 'nullable|max:500|String',
            'segundo_apellido' => 'nullable|max:500|String',
            'nombre_comercial' => 'nullable|max:500|String',
            'edad' => 'nullable|max:500|String',
            'rfc' => 'nullable|max:500|String',
            'curp' => 'nullable|max:500|String',
            'domicilios.*' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 201);
        }
        DB::beginTransaction();
        try{
            $parte = Arr::except($request->all(), ['domicilios','_token','contactos']);
            $audiencia_id = $request->get('audiencia_id');
            $comparece = $request->get('comparece');
            $contactos = $request->get('contactos');
            $domicilios = $request->get('domicilios');
            if(isset($request->asignado)){
                $parte["asignado"] = $request->asignado;
            }

            $parteSaved = Parte::create($parte);
            if ($domicilios && count($domicilios) > 0) {
                foreach ($domicilios as $key => $domicilio) {
                    unset($domicilio['activo']);
                    $domicilioSaved = $parteSaved->domicilios()->create($domicilio);
                }
            }
            if ($contactos && count($contactos) > 0) {
                foreach ($contactos as $key => $contacto) {
                    unset($contacto['activo']);
                    $contactoSaved = $parteSaved->contactos()->create($contacto);
                }
            }
            if($comparece){
                if($audiencia_id != ""){
                    AudienciaParte::create(["audiencia_id" => $audiencia_id, "parte_id" => $parteSaved->id, "tipo_notificacion_id" => 1]);
                }
            }
            DB::commit();
            return $this->sendResponse($parteSaved, 'SUCCESS');
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error'.$e->getMessage());

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Parte $parte)
    {
        return $parte;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Parte $parte)
    {
        return $parte;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parte $parte)
    {
        // $validator = Validator::make($request->all(), [
        //     // 'solicitud_id' => 'required|Integer',
        //     // 'tipo_parte_id' => 'required|Integer',
        //     // 'genero_id' => 'required|Integer',
        //     // 'tipo_persona_id' => 'required|Integer',
        //     // 'nacionalidad_id' => 'required|Integer',
        //     // 'entidad_nacimiento_id' => 'required|Integer',
        //     // 'fecha_nacimiento' => 'required|Date',
        //     'nombre' => 'required|max:500|String',
        //     'primer_apellido' => 'required|max:500|String',
        //     'segundo_apellido' => 'required|max:500|String',
        //     'nombre_comercial' => 'required|max:500|String',
        //     // 'edad' => 'required|max:500|String',
        //     // 'rfc' => 'required|max:500|String',
        //     // 'curp' => 'required|max:500|String',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json($validator, 201);
        //                 // ->withInput();
        // }
        DB::beginTransaction();
        try{

            $parte->update($request->all());
            $domicilio = $request->domicilio;
            unset($domicilio['id']);
            unset($domicilio['activo']);
            $domicilioSaved = $parte->domicilios()->create($domicilio);
            DB::commit();
            $parte->update($request->all());

            return $this->sendResponse($parte, 'SUCCESS');
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                " Se emitió el siguiente mensale: ". $e->getMessage().
                " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error'.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parte $parte)
    {
        $parte->delete();
      return response()->json(null,204);
    }

    /**
     * Funcion para obtener el representante legal de una parte
     * @param id $id
     * @return parte
     */
    public function GetRepresentanteLegal($id){
        $representantes = Parte::where("parte_representada_id",$id)->where("representante",true)->get();
        if($representantes != null && $representantes != ""){
            foreach($representantes as $key => $representante){
                foreach($representante->contactos as $key2 => $contactos){
                    $representantes[$key]->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
                    $documentos = $representantes[$key]->documentos;
                    foreach ($documentos as $documento) {
                        $documento->tipo_archivo = $documento->clasificacionArchivo->tipo_archivo_id;
                    }

                }
            }
        }
        return $representantes;
    }
    /**
     * Funcion para obtener el representante legal de una parte
     * @param id $id
     * @return parte
     */
    public function GetRepresentanteAudiencia(Request $request){
        $audiencia_id= $request->audiencia_id;
        $representante = Parte::where("parte_representada_id",$request->parte_id)->where("representante",true)->with('audienciaParte')->whereHas('audienciaParte',function($query) use ($audiencia_id){
            $query->where('audiencia_id',$audiencia_id);
        })->first();
        if($representante != null && $representante != ""){
            foreach($representante->contactos as $key2 => $contactos){
                $representante->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
                $documentos = $representante->documentos;
                foreach ($documentos as $documento) {
                    $documento->tipo_archivo = $documento->clasificacionArchivo->tipo_archivo_id;
                }

            }
        }
        return $representante;
    }
     /**
     * Funcion para obtener el representante legal de una parte
     * @param id $id
     * @return parte
     */
    public function GetRepresentanteLegalById($id){
        $representante = Parte::find($id);
        if($representante != null && $representante != ""){
            foreach($representante->contactos as $key2 => $contactos){
                $representante->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
                $documentos = $representante->documentos;
                foreach ($documentos as $documento) {
                    $documento->tipo_archivo = $documento->clasificacionArchivo->tipo_archivo_id;
                }

            }
        }
        return $representante;
    }



    /**
     * Funcion para obtener datos laborales de una parte
     * @param id $id
     * @return parte
     */
    public function GetDatoLaboral($parte_id){
        $datos_laborales = DatoLaboral::where('parte_id',$parte_id)->get();
        if(count($datos_laborales) > 1){
            $datos_laborales = $datos_laborales->where("resolucion",true)->first();
        }else{
            $datos_laborales = $datos_laborales->first();
        }
        if($datos_laborales){
            return $datos_laborales;
        }else{
            return [];
        }
    }
    /**
     * Funcion para obtener el representante legal de una parte
     * @param id $id
     * @return parte
     */
    public function GuardarDatoLaboral(Request $request){
        $request->validate([
            //'ocupacion_id' => 'required|Integer',
            //'nss' => 'required|String',
            //'no_issste' => 'required|String',
            'remuneracion' => 'required',
            'periodicidad_id' => 'required|Integer',
            'labora_actualmente' => 'required',
            'fecha_ingreso' => 'required|Date',
            //'fecha_salida' => 'required|Date',
            'jornada_id' => 'required|Integer',
            'horas_semanales' => 'required',
            'parte_id' => 'required|Integer'
        ]);
        if($request->resolucion == "true"){
            $datos_laborales = DatoLaboral::find($request->id);

            $datos_laborales->update([
                'ocupacion_id' => $request->ocupacion_id,
                'nss' => $request->nss,
                //'no_issste' => $request->no_issste,
                'remuneracion' => $request->remuneracion,
                'periodicidad_id' => $request->periodicidad_id,
                'labora_actualmente' => $request->labora_actualmente,
                'fecha_ingreso' => $request->fecha_ingreso,
                'fecha_salida' => $request->fecha_salida,
                'jornada_id' => $request->jornada_id,
                'horas_semanales' => $request->horas_semanales,
                'parte_id' => $request->parte_id,
                'resolucion' => true,
                'puesto' => $request->puesto,
                'horario_laboral' => $request->horario_laboral,
                'horario_comida' => $request->horario_comida,
                'comida_dentro' => $request->comida_dentro,
                'dias_descanso' => $request->dias_descanso,
                'dias_vacaciones' => $request->dias_vacaciones,
                'dias_aguinaldo' => $request->dias_aguinaldo,
                'prestaciones_adicionales' => $request->prestaciones_adicionales,
            ]);
        }else{
            $datos_laborales = DatoLaboral::create([
                'ocupacion_id' => $request->ocupacion_id,
                'nss' => $request->nss,
                //'no_issste' => $request->no_issste,
                'remuneracion' => $request->remuneracion,
                'periodicidad_id' => $request->periodicidad_id,
                'labora_actualmente' => $request->labora_actualmente,
                'fecha_ingreso' => $request->fecha_ingreso,
                'fecha_salida' => $request->fecha_salida,
                'jornada_id' => $request->jornada_id,
                'horas_semanales' => $request->horas_semanales,
                'parte_id' => $request->parte_id,
                'resolucion' => true,
                'puesto' => $request->puesto,
                'horario_laboral' => $request->horario_laboral,
                'horario_comida' => $request->horario_comida,
                'comida_dentro' => $request->comida_dentro,
                'dias_descanso' => $request->dias_descanso,
                'dias_vacaciones' => $request->dias_vacaciones,
                'dias_aguinaldo' => $request->dias_aguinaldo,
                'prestaciones_adicionales' => $request->prestaciones_adicionales,
            ]);
        }

        return $datos_laborales;
    }


    function GuardarRepresentanteLegal(Request $request){
        DB::beginTransaction();
        try{
            $exito = true;
            if($request->parte_id != "" && $request->parte_id != null){
                $parte = Parte::find($request->parte_id);
                $parte->update([
                    "curp" => $request->curp,
                    "nombre" => $request->nombre,
                    "primer_apellido" => $request->primer_apellido,
                    "segundo_apellido" => $request->segundo_apellido,
                    "fecha_nacimiento" => $request->fecha_nacimiento,
                    "genero_id" => $request->genero_id,
                    "clasificacion_archivo_id" => $request->clasificacion_archivo_id,
                    "genero_id" => $request->genero_id,
                    "feha_instrumento" => $request->feha_instrumento,
                    "detalle_instrumento" => $request->detalle_instrumento
                ]);
                // Creamos la relacion en audiencias_partes
                if(!isset($request->fuente_solicitud)){
                    $audienciaExiste = AudienciaParte::where('parte_id',$parte->id)->where('audiencia_id',$request->audiencia_id)->first();
                    if($audienciaExiste == null)
                    {

                        $partesRep = Parte::where('parte_representada_id',$parte->parte_representada_id)->get();
                        foreach($partesRep as $parteR){
                            $ap = AudienciaParte::where('audiencia_id',$request->audiencia_id)->where('parte_id',$parteR->id)->first();
                            if($ap){
                                $ap->delete();
                            }
                        }
                        AudienciaParte::create(["audiencia_id" => $request->audiencia_id,"parte_id" => $parte->id]);
                    }
                }
                // se actualiza doc
                if(isset($request->fileIdentificacion)){
                    $parte = Parte::find($request->parte_id);
                    $solicitud = Solicitud::find($request->solicitud_id);

                    try{
                        $existe = true;
                        $deleted = true;
                        $documentos = $parte->documentos;
                        // foreach($documentos as $documento ){
                        //     if($documento->clasificacionArchivo->tipo_archivo_id == 1){
                        //         $doc_del_id = $documento->id;
                        //         $existe = true;
                        //     }
                        // }
                        // if($existe){
                        //     $parte->documentos()->find($doc_del_id)->delete();
                        //     $deleted = true;
                        // }
                        if(!$existe || $deleted){
                            $existeDocumento = $parte->documentos;
                            if($solicitud != null){
                                $archivoIde = $request->fileIdentificacion;
                                $solicitud_id = $solicitud->id;
                                $clasificacion_archivo= $request->tipo_documento_id;
                                $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                                Storage::makeDirectory($directorio);
                                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);

                                $path = $archivoIde->store($directorio);
                                $uuid = Str::uuid();
                                $documento = $parte->documentos()->create([
                                    "nombre" => str_replace($directorio."/", '',$path),
                                    "nombre_original" => str_replace($directorio, '',$archivoIde->getClientOriginalName()),
                                    // "numero_documento" => str_replace($directorio, '',$archivoIde->getClientOriginalName()),
                                    "descripcion" => $tipoArchivo->nombre,
                                    "ruta" => $path,
                                    "uuid" => $uuid,
                                    "tipo_almacen" => "local",
                                    "uri" => $path,
                                    "longitud" => round(Storage::size($path) / 1024, 2),
                                    "firmado" => "false",
                                    "clasificacion_archivo_id" => $tipoArchivo->id ,
                                ]);
                                $exito = true;
                            }else{
                                $exito = false;

                            }
                        }

                    }catch(Exception $e){
                        $exito = false;
                    }
                }
                if(isset($request->fileInstrumento)){

                    $parte = Parte::find($request->parte_id);
                    $solicitud = Solicitud::find($request->solicitud_id);

                    try{
                        $deleted = true;
                        // $documentos = $parte->documentos;
                        $existeInst = true;
                        // foreach($documentos as $documento ){
                        //     if($documento->clasificacionArchivo->tipo_archivo_id == 9){
                        //         $doc_del_idInst = $documento->id;
                        //         $existeInst = true;
                        //     }
                        // }

                        // if($existeInst){

                        //     $parte->documentos()->find($doc_del_idInst)->delete();
                        //     $deleted = true;
                        // }
                        if(!$existeInst || $deleted){
                            $existeDocumento = $parte->documentos;
                            if($solicitud != null){
                                $archivoInst = $request->fileInstrumento;
                                $solicitud_id = $solicitud->id;
                                $clasificacion_archivo= $request->clasificacion_archivo_id;
                                $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                                Storage::makeDirectory($directorio);
                                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);

                                $pathInst = $archivoInst->store($directorio);
                                $uuid = Str::uuid();
                                $documento = $parte->documentos()->create([
                                    "nombre" => str_replace($directorio."/", '',$pathInst),
                                    "nombre_original" => str_replace($directorio, '',$archivoInst->getClientOriginalName()),
                                    // "numero_documento" => str_replace($directorio, '',$archivoInst->getClientOriginalName()),
                                    "descripcion" => $tipoArchivo->nombre,
                                    "ruta" => $pathInst,
                                    "uuid" => $uuid,
                                    "tipo_almacen" => "local",
                                    "uri" => $pathInst,
                                    "longitud" => round(Storage::size($pathInst) / 1024, 2),
                                    "firmado" => "false",
                                    "clasificacion_archivo_id" => $tipoArchivo->id ,
                                ]);
                                $exito = true;
                            }else{
                                $exito = false;

                            }
                        }

                    }catch(Exception $e){
                        $exito = false;
                    }
                }
                if(isset($request->fileCedula)){

                    $parte = Parte::find($request->parte_id);
                    $solicitud = Solicitud::find($request->solicitud_id);

                    try{
                        $deleted = true;
                        // $documentos = $parte->documentos;
                        $existeCed = true;
                        // foreach($documentos as $documento ){
                        //     if($documento->clasificacionArchivo->tipo_archivo_id == 9){
                        //         $doc_del_idCed = $documento->id;
                        //         $existeCed = true;
                        //     }
                        // }

                        // if($existeCed){

                        //     $parte->documentos()->find($doc_del_idCed)->delete();
                        //     $deleted = true;
                        // }
                        if(!$existeCed || $deleted){
                            $existeDocumento = $parte->documentos;
                            if($solicitud != null){
                                $archivoCed = $request->fileCedula;
                                $solicitud_id = $solicitud->id;
                                $clasificacion_archivo= 3;
                                $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                                Storage::makeDirectory($directorio);
                                $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);

                                $pathCed = $archivoCed->store($directorio);
                                $uuid = Str::uuid();
                                $documento = $parte->documentos()->create([
                                    "nombre" => str_replace($directorio."/", '',$pathCed),
                                    "nombre_original" => str_replace($directorio, '',$archivoCed->getClientOriginalName()),
                                    // "numero_documento" => str_replace($directorio, '',$archivoCed->getClientOriginalName()),
                                    "descripcion" => $tipoArchivo->nombre,
                                    "ruta" => $pathCed,
                                    "uuid" => $uuid,
                                    "tipo_almacen" => "local",
                                    "uri" => $pathCed,
                                    "longitud" => round(Storage::size($pathCed) / 1024, 2),
                                    "firmado" => "false",
                                    "clasificacion_archivo_id" => $tipoArchivo->id ,
                                ]);
                                $exito = true;
                            }else{
                                $exito = false;

                            }
                        }

                    }catch(Exception $e){
                        $exito = false;
                    }
                }
                // se actualiza doc
            }else{
                $parte_representada = Parte::find($request->parte_representada_id);
                $parte = Parte::create([
                    "solicitud_id" => $parte_representada->solicitud->id,
                    "tipo_parte_id" => 3,
                    "tipo_persona_id" => 1,
                    "rfc" => "",
                    "curp" => $request->curp,
                    "nombre" => $request->nombre,
                    "primer_apellido" => $request->primer_apellido,
                    "segundo_apellido" => $request->segundo_apellido,
                    "fecha_nacimiento" => $request->fecha_nacimiento,
                    "genero_id" => $request->genero_id,
                    "clasificacion_archivo_id" => $request->clasificacion_archivo_id,
                    "detalle_instrumento" => $request->detalle_instrumento,
                    "genero_id" => $request->genero_id,
                    "feha_instrumento" => $request->feha_instrumento,
                    "detalle_instrumento" => $request->detalle_instrumento,
                    "parte_representada_id" => $request->parte_representada_id,
                    "representante" => true
                ]);
                $listaContactos = json_decode($request->listaContactos);
                foreach($listaContactos as $contacto){
                    $parte->contactos()->create([
                        "contacto" => $contacto->contacto,
                        "tipo_contacto_id" => $contacto->tipo_contacto_id,
                    ]);
                }

                // Creamos la relacion en audiencias_partes
                if(!isset($request->fuente_solicitud)){
                    $audienciaExiste = AudienciaParte::where('parte_id',$parte->id)->where('audiencia_id',$request->audiencia_id)->first();
                    if($audienciaExiste == null)
                    {
                        $partesRep = Parte::where('parte_representada_id',$parte->parte_representada_id)->get();
                        foreach($partesRep as $parteR){
                            $ap = AudienciaParte::where('audiencia_id',$request->audiencia_id)->where('parte_id',$parteR->id)->first();
                            if($ap){
                                $ap->delete();
                            }
                        }
                        AudienciaParte::create(["audiencia_id" => $request->audiencia_id,"parte_id" => $parte->id]);
                    }
                }
                // se agrega doc
                // $parte = $parte_representada;
                $solicitud = Solicitud::find($request->solicitud_id);
                try{
                    if(count($parte->documentos) == 0){
                        $existeDocumento = $parte->documentos;
                        if($solicitud != null){
                            $archivo = $request->fileIdentificacion;
                            $solicitud_id = $solicitud->id;
                            $clasificacion_archivo= $request->tipo_documento_id;
                            $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                            Storage::makeDirectory($directorio);
                            $tipoArchivo = ClasificacionArchivo::find($clasificacion_archivo);

                            $path = $archivo->store($directorio);
                            $uuid = Str::uuid();
                            $documento = $parte->documentos()->create([
                                "nombre" => str_replace($directorio."/", '',$path),
                                "nombre_original" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                                // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                                "descripcion" => $tipoArchivo->nombre,
                                "ruta" => $path,
                                "uuid" => $uuid,
                                "tipo_almacen" => "local",
                                "uri" => $path,
                                "longitud" => round(Storage::size($path) / 1024, 2),
                                "firmado" => "false",
                                "clasificacion_archivo_id" => $tipoArchivo->id ,
                            ]);

                            // Se agregan instruccion

                            $archivoInst = $request->fileInstrumento;
                            $solicitud_id = $solicitud->id;
                            $clasificacion_archivoInst= $request->clasificacion_archivo_id;
                            $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                            Storage::makeDirectory($directorio);
                            $tipoArchivoInst = ClasificacionArchivo::find($clasificacion_archivoInst);

                            $pathInst = $archivoInst->store($directorio);
                            $uuid = Str::uuid();
                            $documento = $parte->documentos()->create([
                                "nombre" => str_replace($directorio."/", '',$pathInst),
                                "nombre_original" => str_replace($directorio, '',$archivoInst->getClientOriginalName()),
                                // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                                "descripcion" => $tipoArchivoInst->nombre,
                                "ruta" => $pathInst,
                                "uuid" => $uuid,
                                "tipo_almacen" => "local",
                                "uri" => $pathInst,
                                "longitud" => round(Storage::size($pathInst) / 1024, 2),
                                "firmado" => "false",
                                "clasificacion_archivo_id" => $tipoArchivoInst->id ,
                            ]);

                            // Se agrega cedula
                            if(isset($request->fileCedula)){
                                $archivoCed = $request->fileCedula;
                                $solicitud_id = $solicitud->id;
                                $clasificacion_archivoCed= 3;
                                $directorio = 'solicitud/' . $solicitud_id.'/parte/'.$parte->id;
                                Storage::makeDirectory($directorio);
                                $tipoArchivoCed = ClasificacionArchivo::find($clasificacion_archivoCed);

                                $pathCed = $archivoCed->store($directorio);
                                $uuid = Str::uuid();
                                $documento = $parte->documentos()->create([
                                    "nombre" => str_replace($directorio."/", '',$pathCed),
                                    "nombre_original" => str_replace($directorio, '',$archivoCed->getClientOriginalName()),
                                    // "numero_documento" => str_replace($directorio, '',$archivo->getClientOriginalName()),
                                    "descripcion" => $tipoArchivoCed->nombre,
                                    "ruta" => $pathCed,
                                    "uuid" => $uuid,
                                    "tipo_almacen" => "local",
                                    "uri" => $pathCed,
                                    "longitud" => round(Storage::size($pathCed) / 1024, 2),
                                    "firmado" => "false",
                                    "clasificacion_archivo_id" => $tipoArchivoCed->id ,
                                ]);
                            }
                            $exito = true;
                        }else{
                            $exito = false;

                        }
                    }

                }catch(Exception $e){
                    $exito = false;

                }
                // se actualiza doc
            }
            if($exito){
                DB::commit();
                return $parte;
            }else{
                DB::rollback();
                return $this->sendError('Error al capturar representante', 'Error');
            }

        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error al capturar representante', 'Error');

        }
    }

    public function AgregarContactoRepresentante(Request $request){
        $representante = Parte::find($request->parte_id);
        $representante->contactos()->create(["tipo_contacto_id" => $request->tipo_contacto_id,"contacto" => $request->contacto]);
        foreach($representante->contactos as $key2 => $contactos){
            $representante->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
        }
        return $representante->contactos;
    }

    public function getParteCurp(Request $request){
        $Parte = Parte::where('curp',$request->curp)->orderBy('id', 'desc')->first();
        return $Parte;
    }
    public function EliminarContactoRepresentante(Request $request){
        $contacto = Contacto::find($request->contacto_id)->delete();
        $representante = Parte::find($request->parte_id);
        foreach($representante->contactos as $key2 => $contactos){
            $representante->contactos[$key2]->tipo_contacto = $contactos->tipo_contacto;
        }
        return $this->sendResponse($representante->contactos, 'SUCCESS');
    }
    public function getDomicilioParte(){
        $parte = Parte::find($this->request->id);
        return $parte->domicilios[0];
    }
    public function getParteSolicitud($id){
        $parte = Parte::find($id);
        if($parte->tipo_parte_id == 3){// representante legal
            $idParteSolicitud = $parte->parte_representada_id;
        }else{
            $idParteSolicitud = $parte->id;
        }
        return response()->json($idParteSolicitud, 200);
        //return $idParteSolicitud;
    }

    public function cambiarDomicilioParte(){
        $domicilio = Domicilio::find($this->request->domicilio["id"]);
        $dom =(array) $this->request->domicilio;
        unset($dom["activo"]);
        $domicilio->update($dom);
        return $domicilio;
    }
    public function getPartesComboDocumentos() {
        $solicitud = Solicitud::find($this->request->solicitud_id);
        $partes = $solicitud->partes()->whereIn("tipo_parte_id",[1,2,3])->get();
        return $partes;
    }
    public function validarCorreoParte(){
        $parte = Parte::find($this->request->parte_id);
        $array = array();
        $pasa = false;
        foreach($parte->contactos as $contacto){
            if($contacto->tipo_contacto_id == 3){ //si tiene email
                $pasa = true;
            }
        }
        if($parte->correo_buzon){
            $pasa = true;
        }
            //devuelve partes sin email

        $parte->tieneCorreo = $pasa;
        return $parte;
    }

    public function getCitadosBySolicitudId($solicitud_id){
        $partes = Solicitud::find($solicitud_id)->partes()->with(['domicilios'=>function($q){$q->orderBy('id','desc');}])->where('tipo_parte_id',2)->get();
        return $this->sendResponse($partes, 'SUCCESS');
    }
    public function updateCitadosDomicilio(Request $request){
        DB::beginTransaction();
        try{
            $parte = $request->parte;
            $parteUpd = Parte::find($parte->id);
            $parteUpd->update(['nombre'=>$parte->nombre,'primer_apellido'=>$parte->primer_apellido,'segundo_apellido'=>$parte->segundo_apellido,'nombre_comercial'=>$parte->nombre_comercial]);
            $parteUpd->domicilios($parte->domicilio);
            DB::commit();
            return $this->sendResponse($parteUpd, 'SUCCESS');
        }catch(Exception $e){
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                " Se emitió el siguiente mensale: ". $e->getMessage().
                " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            DB::rollback();
            return $this->sendError('Error'.$e->getMessage());
        }
    }

    public function aceptar_buzon(Request $request){
        $parte = Parte::find($request->parte_id);
        $solicitud = $parte->solicitud;
        $notificacion_buzon = $request->acepta_buzon;
        if($parte){
            if($notificacion_buzon == "true"){
                $parte->update(['notificacion_buzon'=>$notificacion_buzon, 'fecha_aceptacion_buzon'=>$fechaFin = Carbon::now()]);
                $identificador = $parte->rfc;
                if($parte->tipo_persona_id == $tipo->id){
                    $identificador = $parte->curp;
                }
                //Genera acta de aceptacion de buzón
                if($parte->tipo_parte_id == 1){
                    event(new GenerateDocumentResolution("", $solicitud->id, 62, 19,$parte->id));
                }else{
                    event(new GenerateDocumentResolution("", $solicitud->id, 62, 20,null,$parte->id));
                }
                BitacoraBuzon::create(['parte_id'=>$parte->id,'descripcion'=>'Se genera el documento de aceptación de buzón electrónico','tipo_movimiento'=>'Documento','identificador' => $identificador]);
            }else{
                $parte->update(['notificacion_buzon'=>$notificacion_buzon]);
                $existe = $parte->documentos()->where('clasificacion_archivo_id',1)->first();
                if($existe == null){
                    //Genera acta de no aceptacion de buzón
                    if($parte->tipo_parte_id == 1){
                        event(new GenerateDocumentResolution("", $solicitud->id, 60, 22,$parte->id));
                    }else{
                        event(new GenerateDocumentResolution("", $solicitud->id, 60, 23,null,$parte->id));
                    }
                }
            }
            return $this->sendResponse($parte, 'SUCCESS');
        }
        return $this->sendError('Error al aceptar buzón', 'Error');
    }
}
