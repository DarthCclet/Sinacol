<?php

namespace App\Listeners;

use App\Events\RatificacionRealizada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Audiencia;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Incidencia;
use App\Traits\FechaNotificacion;
use App\HistoricoNotificacion;
use App\HistoricoNotificacionRespuesta;

class SendNotificacion
{
    use FechaNotificacion;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RatificacionRealizada  $event
     * @return void
     */
    public function handle(RatificacionRealizada $event)
    {
        try{
            DB::beginTransaction();
            //Creamos una instancia a solicitudesController para usar las funciones que contiene
            $arreglo = array();
            // Consultamos la audiencia
            $audiencia = Audiencia::find($event->audiencia_id);
            if(isset($audiencia->id)){
                $tipo_notificacion = self::ObtenerTipoNotificacion($audiencia);
                // Agregamos al arreglo las generalidades de la audiencia
                $arreglo["folio"] = $audiencia->folio."/".$audiencia->anio;
                $arreglo["expediente"] = $audiencia->expediente->folio."/";
                $arreglo["exhorto_num"] = "";
                //Validamos el tipo de notificación
                $fechaIngreso = new \Carbon\Carbon($audiencia->expediente->solicitud->created_at);
                if($tipo_notificacion == "citatorio"){
                    $fechaRecepcion = new \Carbon\Carbon($audiencia->expediente->solicitud->fecha_ratificacion);
                    $fechaAudiencia = new \Carbon\Carbon($audiencia->fecha_audiencia);
                    $fechaCita = null;
                    if($audiencia->fecha_cita != null && $audiencia->fecha_cita != ""){
                        $fechaCita = new \Carbon\Carbon($audiencia->fecha_cita);
                    }
                    $fechaLimite = new \Carbon\Carbon($audiencia->fecha_limite_audiencia);
                    $arreglo["fecha_recepcion"] = $fechaRecepcion->format("d/m/Y");
                    $arreglo["fecha_audiencia"] = $fechaAudiencia->format("d/m/Y");
                    $arreglo["fecha_ar"] = $fechaRecepcion->format("d/m/Y");
                    if($fechaCita != null){
                        $arreglo["fecha_cita"] = $fechaCita->format("d/m/Y");
                    }else{
                        $arreglo["fecha_cita"] = null;
                    }
                    $arreglo["fecha_limite"] = $fechaLimite->format("d/m/Y");
                }else{
                    $fechaRecepcion = new \Carbon\Carbon($audiencia->fecha_audiencia);
                    $fechaAudiencia = self::ObtenerFechaAudienciaMulta($audiencia->fecha_audiencia,15);
                    $fechaCita = null;
                    $fechaLimite = new \Carbon\Carbon(self::ObtenerFechaLimiteNotificacionMulta($fechaAudiencia,$audiencia->expediente->solicitud,$audiencia->id));
                    $arreglo["fecha_recepcion"] = $fechaRecepcion->format("d/m/Y");
                    $arreglo["fecha_audiencia"] = $fechaAudiencia->format("d/m/Y");
                    $arreglo["fecha_ar"] = $fechaRecepcion->format("d/m/Y");
                    $arreglo["fecha_limite"] = $fechaLimite->format("d/m/Y");
                    $arreglo["fecha_cita"] = null;
                }
                $arreglo["fecha_ingreso"] = $fechaIngreso->format("d/m/Y");
                $arreglo["nombre_junta"] = $audiencia->expediente->solicitud->centro->nombre;
                $arreglo["junta_id"] = $audiencia->expediente->solicitud->centro_id;
                $arreglo["tipo_notificacion"] = $tipo_notificacion;
                //Buscamos a los actores
                Log::debug('Información de solicitud:'.json_encode($arreglo));
                $actores = self::getSolicitantes($audiencia);
                foreach($actores as $partes){
                    $parte =$partes->parte;
                    if($parte->tipo_persona_id == 1){
                        $nombre = $parte->nombre." ".$parte->primer_apellido." ".$parte->segundo_apellido;
                        if($parte->genero_id == 1){
                            $sexo = "Hombre";
                        }else{
                            $sexo = "Mujer";
                        }
                    }else{
                        $nombre = $parte->nombre_comercial;
                        $sexo = "";
                    }
                    $domicilio = $parte->domicilios->first();
                    $arreglo["Actores"][] = array(
                        "actor_id" => $parte->id,
                        "nombre" => $nombre,
                        "sexo" => $sexo,
                        "tipo_persona" => $parte->tipoPersona->nombre,
                        "Direccion" => array(
                            "estado" => $domicilio->estado,
                            "estado_id" => $domicilio->estado_id,
                            "delegacion" => $domicilio->municipio,
                            "colonia" => $domicilio->asentamiento,
                            "cp" => $domicilio->cp,
                            "tipo_vialidad" => $domicilio->tipo_vialidad,
                            "calle" => $domicilio->vialidad,
                            "num_ext" => $domicilio->num_ext,
                            "num_int" => $domicilio->num_int,
                            "en_catalogo" => true,
                            "latitud" => $domicilio->latitud,
                            "longitud" => $domicilio->longitud
                        )
                    );
                }
                Log::debug('Información de actores:'.json_encode($arreglo["Actores"]));
                //Buscamos a los demandados
                $demandados = self::getSolicitados($audiencia,$tipo_notificacion,$event->parte_id);
                foreach($demandados as $partes){
                    $parte =$partes->parte;
                    if($parte->tipo_persona_id == 1){
                        $nombre = $parte->nombre." ".$parte->primer_apellido." ".$parte->segundo_apellido;
                    }else{
                        $nombre = $parte->nombre_comercial;
                    }
                    $domicilio = $parte->domicilios->first();
                    $arreglo["Demandados"][] = array(
                        "demandado_id" => $parte->id,
                        "actuario_id" => 999999,
                        "nombre" => $nombre,
                        "sexo" => "",
                        "tipo_persona" => $parte->tipoPersona->nombre,
                        "Direccion" => array(
                            "estado" => $domicilio->estado,
                            "estado_id" => $domicilio->estado_id,
                            "delegacion" => $domicilio->municipio,
                            "colonia" => $domicilio->asentamiento,
                            "cp" => $domicilio->cp,
                            "tipo_vialidad" => $domicilio->tipo_vialidad,
                            "calle" => $domicilio->vialidad,
                            "num_ext" => $domicilio->num_ext,
                            "num_int" => $domicilio->num_int,
                            "en_catalogo" => true,
                            "latitud" => $domicilio->latitud,
                            "longitud" => $domicilio->longitud
                        )
                    );
    //                Buscamos si existe un historico de ese tipo de notificación
                    $historico = HistoricoNotificacion::where("audiencia_parte_id",$partes->id)->where("tipo_notificacion",$event->tipo_notificacion)->first();
                    if($historico == null){
                        $historico = HistoricoNotificacion::create([
                            "audiencia_parte_id" => $partes->id,
                            "tipo_notificacion" => $event->tipo_notificacion,
                        ]);
                    }
                    HistoricoNotificacionRespuesta::create([
                        "historico_notificacion_id" => $historico->id,
                        "etapa_notificacion_id" => $audiencia->etapa_notificacion_id,
                        "fecha_peticion" => now()
                    ]);
                }
                Log::debug('Información de demandados:'.json_encode($arreglo["Demandados"]));
                Log::debug('Se envia esta peticion:'.json_encode($arreglo));
                $client = new Client();
                if(env('NOTIFICACION_DRY_RUN') == "YES"){
                    $baseURL = env('APP_URL_NOTIFICACIONES');
                }else{
                    $baseURL = $audiencia->expediente->solicitud->centro->url_instancia_notificacion;
                }
                if($baseURL != null){
                    $response = $client->request('POST',$baseURL ,[
                        'headers' => ['foo' => 'bar'],
                        'verify' => false,
                        'body' => json_encode($arreglo),
                    ]);
                }
        //        Cambiamos el estatus de notificación
                $solicitud = $audiencia->expediente->solicitud;
                $solicitud->update(["fecha_peticion_notificacion" => now()]);
            }
            
            DB::commit();
//        }catch (\Throwable $e) {
//            DB::rollBack();
//            Log::error('En scriptt:'.$e->getFile()." En línea: ".$e->getLine().
//                       " Se emitió el siguiente mensale: ". $e->getMessage().
//                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $respuesta = $response->getBody()->getContents();
            if($respuesta == '{"error":"La notificaci\u00f3n ya existe en el sistema ","detalle":"Se ha actualizado la informaci\u00f3n del expediente."}'){
                $solicitud = $audiencia->expediente->solicitud;
                $solicitud->update(["fecha_peticion_notificacion" => now()]);
                DB::commit();
                Log::warning('En scripts:'.$e->getFile()." En línea: ".$e->getLine().
                           " Se emitió el siguiente mensale: ". $respuesta.
                           " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            }else{
                DB::rollBack();
                Log::error('respuesta En scripts:'.$e->getFile()." En línea: ".$e->getLine().
                           " Se emitió el siguiente mensale: ". $respuesta.
                           " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
            }
        }catch (Exception $e){
            DB::rollBack();
            Log::error('php En scripts:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $respuesta.
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
        }

    }
    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitante
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitante
     */
    public function getSolicitantes(Audiencia $audiencia) {
        $solicitantes = [];
        foreach ($audiencia->audienciaParte as $parte) {
            if ($parte->parte->tipo_parte_id == 1) {
                $solicitantes[] = $parte;
            }
        }
        return $solicitantes;
    }

    /**
     * Funcion para obtener las partes involucradas en una audiencia de tipo solicitado
     * @param Audiencia $audiencia
     * @return AudienciaParte $solicitado
     */
    public function getSolicitados(Audiencia $audiencia,$tipo_notificacion,$audiencia_parte_id = null) {
        $solicitados = [];
        foreach ($audiencia->audienciaParte as $parte) {
            if($parte->finalizado == null || $tipo_notificacion == "multa"){
                if($audiencia_parte_id == null){
                    if ($parte->parte->tipo_parte_id == 2) {
                        $solicitados[] = $parte;
                    }
                }else{
                    if($parte->id == $audiencia_parte_id){
                        $solicitados[] = $parte;
                    }
                }
            }
        }
        return $solicitados;
    }

    /**
     * Funcion para obtener la fecha de audiencia de multa
     * @param String $fecha_audiencia
     * @return Carbon $fecha_audiencia
     */
    public function ObtenerFechaAudienciaMulta($fecha_audiencia,$dias){
        $d = new \Carbon\Carbon($fecha_audiencia);
        $diasRecorridos = 1;
        while ($diasRecorridos < $dias){
            $sig = $d->addDay()->format("Y-m-d");
            if(!Incidencia::hayIncidencia($sig,auth()->user()->centro_id,"App\Centro")){
                $d = new \Carbon\Carbon($sig);
                $diasRecorridos++;
            }
        }
        return $d;
    }
    /**
     * 
     */
    Public function ObtenerFechaLimiteNotificacionMulta($fecha_audiencia,$solicitud,$audiencia_id){
//      obtenemos el domicilio del centro
        $domicilio_centro = auth()->user()->centro->domicilio;
//      obtenemos el domicilio del citado
        $partes = $solicitud->partes;
        $domicilio_citado = null;
        foreach($partes as $parte){
            if($parte->tipo_parte_id == 2){
                $domicilio_citado = $parte->domicilios()->first();
                break;
            }
        }
        if($domicilio_citado->latitud == "" || $domicilio_citado->longitud == ""){
            return date("Y-m-d");
        }else{
            return self::obtenerFechaLimiteNotificacion($domicilio_centro,$domicilio_citado,$fecha_audiencia);
        }
    }
    public function ObtenerTipoNotificacion($audiencia){
        $clasificacion_multa = \App\ClasificacionArchivo::where("nombre","Acta de multa")->first();
//        dump($audiencia->documentos);
        if(isset($audiencia->documentos)){
            $doc = $audiencia->documentos()->where("clasificacion_archivo_id",$clasificacion_multa->id)->first();
            if($doc == null){
                return "citatorio";
            }else{
                return "multa";
            }
        }
        return "citatorio";
    }
}
