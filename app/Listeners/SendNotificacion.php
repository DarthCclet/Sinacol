<?php

namespace App\Listeners;

use App\Events\RatificacionRealizada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Audiencia;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendNotificacion
{
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
            // Agregamos al arreglo las generalidades de la audiencia
            $arreglo["folio"] = $audiencia->folio."/".$audiencia->anio;
            $arreglo["expediente"] = $audiencia->expediente->folio."/";
            $arreglo["exhorto_num"] = "";
            $fechaIngreso = new \Carbon\Carbon($audiencia->expediente->solicitud->created_at);
            $fechaRecepcion = new \Carbon\Carbon($audiencia->expediente->solicitud->fecha_ratificacion);
            $fechaAudiencia = new \Carbon\Carbon($audiencia->fecha_audiencia);
            $fechaCita = null;
            if($audiencia->fecha_cita != null && $audiencia->fecha_cita != ""){
                $fechaCita = new \Carbon\Carbon($audiencia->fecha_cita);
            }
            $fechaLimite = new \Carbon\Carbon($audiencia->fecha_limite_audiencia);
            $arreglo["fecha_ingreso"] = $fechaIngreso->format("d/m/Y");
            $arreglo["fecha_recepcion"] = $fechaRecepcion->format("d/m/Y");
            $arreglo["fecha_audiencia"] = $fechaAudiencia->format("d/m/Y");
            $arreglo["fecha_ar"] = $fechaRecepcion->format("d/m/Y");
            $arreglo["nombre_junta"] = $audiencia->expediente->solicitud->centro->nombre;
            $arreglo["junta_id"] = $audiencia->expediente->solicitud->centro_id;
            if($fechaCita != null){
                $arreglo["fecha_cita"] = $fechaCita->format("d/m/Y");
            }else{
                $arreglo["fecha_cita"] = null;
            }
            $arreglo["fecha_limite"] = $fechaLimite->format("d/m/Y");
            $arreglo["tipo_notificacion"] = $event->tipo_notificacion;
            //Buscamos a los actores
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
            //Buscamos a los demandados
            $demandados = self::getSolicitados($audiencia);
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
            }
            Log::debug('Se envia esta peticion:'.json_encode($arreglo));
            $client = new Client();
            $baseURL = $audiencia->expediente->solicitud->centro->url_instancia_notificacion;
            if($baseURL != null){
                $response = $client->request('POST',$baseURL ,[
                    'headers' => ['foo' => 'bar'],
                'verify' => false,
                    // array de datos del formulario
                    'body' => json_encode($arreglo),
        //            'http_errors' => false
                ]);
            }
    //        Cambiamos el estatus de notificación
            $solicitud = $audiencia->expediente->solicitud;
            $solicitud->update(["fecha_peticion_notificacion" => now()]);
            DB::commit();
        }catch (\Throwable $e) {
            DB::rollBack();
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
                       " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            DB::rollBack();
            Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                       " Se emitió el siguiente mensale: ". $e->getMessage().
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
    public function getSolicitados(Audiencia $audiencia) {
        $solicitados = [];
        foreach ($audiencia->audienciaParte as $parte) {
            if ($parte->parte->tipo_parte_id == 2) {
                $solicitados[] = $parte;
            }
        }
        return $solicitados;
    }
}
