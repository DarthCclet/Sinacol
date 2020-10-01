<?php

namespace App\Listeners;

use App\Events\RatificacionRealizada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Audiencia;
use App\Http\Controllers\AudienciaController;

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
        //Creamos una instancia a solicitudesController para usar las funciones que contiene
        $request = new \Illuminate\Support\Facades\Request();
        $AudienciaController = new AudienciaController($request);
        $arreglo = array();
        // Consultamos la audiencia
        $audiencia = Audiencia::find($event->audiencia_id);
        // Agregamos al arreglo las generalidades de la audiencia
        $arreglo["folio"] = $audiencia->folio."/".$audiencia->anio;
        $arreglo["expediente"] = $audiencia->expediente->folio."/".$audiencia->expediente->anio;
        $arreglo["exhorto_num"] = "";
        $arreglo["fecha_ingreso"] = $audiencia->expediente->solicitud->created_at;
        $arreglo["fecha_recepcion"] = $audiencia->expediente->solicitud->fecha_ratificacion;
        $arreglo["fecha_audiencia"] = $audiencia->fecha_audiencia;
        $arreglo["fecha_audiencia"] = $audiencia->fecha_audiencia;
        $arreglo["fecha_ar"] = "";
        $arreglo["nombre_junta"] = $audiencia->expediente->solicitud->centro->nombre;
        $arreglo["junta_id"] = $audiencia->expediente->solicitud->centro_id;
        //Buscamos a los actores
        $actores = $AudienciaController->getSolicitantes($audiencia);
        dd($actores);
        foreach($actores as $partes){
            if($parte->tipo_persona_id == 1){
                $nombre = $parte->nombre." ".$parte->primer_apellido." ".$parte->segundo_apellido;
                if($parte->genero_id == 1){
                    $sexo = "Hombre";
                }else{
                    $sexo = "Mujer";
                }
            }else{
                $nombre = $parte->nombre_comercial;
                $sexo = "Hombre";
            }
            $domicilio = $parte->domicilios;
            $actor = array(
                "actor_id" => $parte->id,
                "nombre" => $nombre,
                "sexo" => $sexo,
                "Direccion" => array(
                    "estado" => "",
                    "estado_id" => "",
                    "delegacion" => "",
                    "colonia" => "",
                    "cp" => "",
                    "tipo_vialidad" => "",
                    "calle" => "",
                    "num_ext" => "",
                    "num_int" => ""
                )
            );
        }
    }
}
