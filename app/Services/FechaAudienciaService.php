<?php
namespace App\Services;

use App\Centro;
use App\Sala;
use App\Conciliador;
use App\Audiencia;
use App\Incidencia;
use Carbon\Carbon;
use App\Traits\ValidateRange;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;

class FechaAudienciaService{
    use ValidateRange;
    /**
     * Determina la próxima fecha hábil para una cita.
     * @param string $hoy
     * @param string $asunto
     * @param string $horario
     * @param int $junta
     * @param int $add Por default son tres días hábiles.
     * @return mixed|string|void
     */
    public static function proximaFechaCita(string $hoy, Centro $centro, $add=3)
    {


//        Obtenemos la primer fecha habil del centro despues de tres días en la que el centro labore
        $diaHabilCentro = Incidencia::siguienteDiaHabilMasDias($hoy,$centro->id ,"App\Centro",$add);
        
//        Recorremos las salas del centro
        $d = new Carbon($diaHabilCentro);
        $encontroSala = false;
        $horaInicioSalaDisponible = "00:00:00";
        $horaFinSalaDisponible = "23:59:59";
        $sala_id = null;
        foreach($centro->salas()->inRandomOrder()->get() as $sala){
            if(!$sala->virtual){
                foreach($sala->disponibilidades as $disponibilidad){
                    if($d->weekday() == $disponibilidad->dia){
                        $hora_inicio_sala = $disponibilidad->hora_inicio;
                        $hora_fin_sala = $disponibilidad->hora_fin;
                        
//                        Obtenemos las audiencias que pertenecen a la sala en la fecha
                        $audiencias = Audiencia::join('salas_audiencias', 'audiencias.id', '=', 'salas_audiencias.audiencia_id')
                            ->select('audiencias.*')
                            ->where("audiencias.fecha_audiencia",$diaHabilCentro)
                            ->where("salas_audiencias.sala_id",$sala->id)
                            ->get();                        
                        
                        if(count($audiencias) > 0){
                            $horaFinAsignacion = self::getHoraFinalNuevaAudiencia($diaHabilCentro,$audiencias,$centro->duracionAudiencia);
                            if($horaFinAsignacion <= $hora_fin_sala){
                                $ultima_audiencia_dia = $audiencias->last();
                                $horaInicioSalaDisponible = $ultima_audiencia_dia->hora_fin;
                                $horaFinSalaDisponible = $horaFinAsignacion;
                                $encontroSala=true;
                                $sala_id = $sala->id;
                                break;
                            }
                        }else{
                            $horaInicioSalaDisponible = $hora_inicio_sala;
                            $valores = self::obtenerValoresSuma($centro->duracionAudiencia);
                            $fechaFin = date('H:i:s',strtotime('+'.$valores[0].' hour +'.$valores[1].' minutes +'.$valores[2].' seconds',strtotime($hora_inicio_sala)));
                            $horaFinSalaDisponible =$fechaFin;
                            $encontroSala=true;
                            $sala_id = $sala->id;
                            break;
                        }
                    }
                }
            }
        }
        if($encontroSala){
            $encontroConciliador = false;
            foreach($centro->conciliadores()->inRandomOrder()->get() as $conciliador){
                foreach($conciliador->disponibilidades as $disponibilidad){
                    if($d->weekday() == $disponibilidad->dia){
                        $audiencias = Audiencia::join('conciliadores_audiencias', 'audiencias.id', '=', 'conciliadores_audiencias.audiencia_id')
                            ->select('audiencias.*')
                            ->where("audiencias.fecha_audiencia",$diaHabilCentro)
                            ->where("conciliadores_audiencias.conciliador_id",$conciliador->id)
                            ->get();
                        if(count($audiencias) > 0){
                            $choca_audiencia = false;
                            foreach($audiencias as $audiencia){
                                $hora_inicio_audiencia = $audiencia->hora_inicio;
                                $hora_fin_audiencia = $audiencia->hora_fin;
                                $hora_inicio_audiencia_nueva = $horaInicioSalaDisponible;
                                $hora_fin_audiencia_nueva = $horaFinSalaDisponible;
                                
                                if(!self::rangesNotOverlapOpen($hora_inicio_audiencia, $hora_fin_audiencia, $hora_inicio_audiencia_nueva, $hora_fin_audiencia_nueva)){
                                    $choca_audiencia = true;
                                }
                            }
                            if(!$choca_audiencia){
                                $encontroConciliador = true;
                                $conciliador_id = $conciliador->id;
                            }
                        }else{
                            $encontroConciliador = true;
                            $conciliador_id = $conciliador->id;
                        }
                    }
                }
            }
            if($encontroConciliador){
                return array(
                    "fecha_audiencia" => $diaHabilCentro,
                    "hora_inicio" => $horaInicioSalaDisponible,
                    "hora_fin" => $horaFinSalaDisponible,
                    "sala_id" => $sala_id,
                    "conciliador_id" => $conciliador_id);
            }else{
                return self::proximaFechaCita($diaHabilCentro, $centro, 0);
            }
        } else {
            return self::proximaFechaCita($diaHabilCentro, $centro, 0);
        }
    }
    public static function getHoraFinalNuevaAudiencia($fecha,$audiencias,$duracion){
        $ultima_audiencia_dia = $audiencias->last();
        $valores = self::obtenerValoresSuma($duracion);
        $fecha_asignable = date('H:i:s',strtotime('+'.$valores[0].' hour +'.$valores[1].' minutes +'.$valores[2].' seconds',strtotime($ultima_audiencia_dia->hora_fin)));
        return $fecha_asignable;
    }
    public static function obtenerValoresSuma($duracion){
        return $separa = explode(":",$duracion);
    }
    public static function rangesNotOverlapOpen($start_time1,$end_time1,$start_time2,$end_time2)
    {
      $utc = new DateTimeZone('UTC');

      $start1 = new DateTime($start_time1,$utc);
      $end1 = new DateTime($end_time1,$utc);
      if($end1 < $start1)
        throw new Exception('Range is negative.');

      $start2 = new DateTime($start_time2,$utc);
      $end2 = new DateTime($end_time2,$utc);
      if($end2 < $start2)
        throw new Exception('Range is negative.');

      return ($end1 <= $start2) || ($end2 <= $start1);
    }
}
