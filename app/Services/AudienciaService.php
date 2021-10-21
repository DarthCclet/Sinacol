<?php
namespace App\Services;
use App\Http\Controllers\ContadorController;
use Illuminate\Support\Facades\Log;
use App\Services\FechaAudienciaService;
use App\Sala;
use App\Solicitud;
use App\Centro;
use App\Traits\FechaNotificacion;
use App\Services\DiasVigenciaSolicitudService;

/**
 * Clase para la confirmación de audiencia
 * Class AudienciaService
 */
class AudienciaService{
    use FechaNotificacion;

    public static function obtenerFolios(){
        try{
            $ContadorController = new ContadorController();
            $folioC = $ContadorController->getContador(1,auth()->user()->centro_id);
            $folioAudiencia = $ContadorController->getContador(3, auth()->user()->centro_id);
            return array("folios" => true, "expediente" => $folioC, "audiencia" => $folioAudiencia);
        }catch(Exception $e) {
            Log::error("Al obtener los folios");
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return array("folios" => false);
        }
    }
    public static function obtenerAsignacion(Solicitud $solicitud,$inmediata,$separados,$fecha_cita,$tipo_notificacion_id){
        try{
            if($inmediata == "true"){
                $sala = Sala::where("centro_id", $solicitud->centro_id)->where("virtual", true)->first();
                if ($sala == null) {
                    return array("error" => true,"mensaje" => "No hay salas virtuales disponibles");
                }
                $sala_id = $sala->id;
                $sala2_id = null;
                //                Validamos que el que ratifica sea conciliador
                if (!auth()->user()->hasRole('Personal conciliador')) {
                    return array("error" => true,"mensaje" => "La solicitud con convenio solo puede ser confirmada por personal conciliador");
                } else {
                    //Buscamos el conciliador del usuario
                    if (isset(auth()->user()->persona->conciliador)) {
                        $conciliador_id = auth()->user()->persona->conciliador->id;
                    } else {
                        return array("error" => true,"mensaje" => "El usuario no esta dado de alta en la lista de conciliadores");
                    }
                }
                $conciliador2_id = null;

                $fecha_audiencia = now()->format("Y-m-d");
                $hora_inicio = now()->format('H:i:s');
                $hora_fin = \Carbon\Carbon::now()->addHours(1)->addMinutes(30)->format('H:i:s');
                $multiple = false;
                $encontro_audiencia = true;
                $fecha_notificacion = now();
            }else{
                if ((int) $tipo_notificacion_id == 1) {
                    $diasHabilesMin = 7;
                    $diasHabilesMax = 10;
                } else {
                    $diasHabilesMin = 15;
                    $diasHabilesMax = 18;
                }
                //                obtenemos el domicilio del centro
                $domicilio_centro = auth()->user()->centro->domicilio;
                //                obtenemos el domicilio del citado
                $domicilio_citado = null;
                foreach ($solicitud->partes as $parte) {
                    if ($parte->tipo_parte_id == 2) {
                        $domicilio_citado = $parte->domicilios->last();
                        break;
                    }
                }
                $user_id = auth()->user()->id;
                $centroResponsable = auth()->user()->centro;
                if ($solicitud->tipo_solicitud_id == 3 || $solicitud->tipo_solicitud_id == 4) {
                    $centroResponsable = Centro::where("abreviatura", "OCCFCRL")->first();
                }
                if ($separados == "true") {
                    $datos_audiencia = FechaAudienciaService::obtenerFechaAudienciaDoble(date("Y-m-d"), $centroResponsable, $diasHabilesMin, $diasHabilesMax, $solicitud->virtual);
                    $multiple = true;
                } else {
                    $datos_audiencia = FechaAudienciaService::obtenerFechaAudiencia(date("Y-m-d"), $centroResponsable, $diasHabilesMin, $diasHabilesMax, $solicitud->virtual);
                    $multiple = false;
                }

                $fecha_notificacion = null;
                if ($tipo_notificacion_id == 2) {
                    $fecha_notificacion = self::obtenerFechaLimiteNotificacion($domicilio_centro, $domicilio_citado, $datos_audiencia["fecha_audiencia"]);
                }
                
                $encontro_audiencia = $datos_audiencia["encontro_audiencia"];
                $fecha_audiencia = $datos_audiencia["fecha_audiencia"];
                $hora_inicio = $datos_audiencia["hora_inicio"];
                $hora_fin = $datos_audiencia["hora_fin"];
                $sala_id = $datos_audiencia["sala_id"];
                if($multiple){
                    $sala2_id = $datos_audiencia["sala2_id"];
                    $conciliador2_id = $datos_audiencia["conciliador2_id"];
                }else{
                    $sala2_id = null;
                    $conciliador2_id = null;
                }
                $conciliador_id = $datos_audiencia["conciliador_id"];
            }
            if ($fecha_cita != "" && $fecha_cita != null) {
                $fechaC = explode("/", $fecha_cita);
                $fecha_cita = $fechaC["2"] . "-" . $fechaC["1"] . "-" . $fechaC["0"];
            }
            $etapa = $etapa = \App\EtapaNotificacion::where("etapa", "ilike", "%Ratificación%")->first();
            return array(
                "error" => false,
                "encontro_audiencia" => $encontro_audiencia,
                "fecha_audiencia" => $fecha_audiencia,
                "hora_inicio" => $hora_inicio,
                "hora_fin" => $hora_fin,
                "sala_id" => $sala_id, 
                "sala2_id" => $sala2_id, 
                "conciliador_id" => $conciliador_id,
                "conciliador2_id" => $conciliador2_id,
                "fecha_cita" => $fecha_cita,
                "multiple" => $multiple,
                "fecha_notificacion" => $fecha_notificacion,
                "etapa_id" => $etapa->id
            );
        }catch(Exception $e) {
            Log::error("Al obtener los datos de la audiencia");
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensale: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return array("error" => true,"mensaje" => "No se pudieron obtener los datos de la audiencia");
        }
    }
}