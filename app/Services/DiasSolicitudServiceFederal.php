<?php

namespace App\Services;

use App\Solicitud;
use Carbon\Carbon;

/**
 * Provee metodos para validar si una solicitud aun se puede operar
 * Class DiasSolicitudServiceFederal
 * @package Audiencias
 */
class DiasSolicitudServiceFederal implements DiasSolicitudService
{
    /**
     * Funcion para validar si una solicitud aun se puede operar
     * @param int $solicitud_id
     * @param string $fecha_solicitada
     * @return bool
     */
    public function getSolicitudOperante($solicitud_id,$fecha_solicitada){
        $solicitud = Solicitud::find($solicitud_id);
        $dias = 1;
        if($solicitud->tipo_solicitud_id == 1){
            $dt = new Carbon($solicitud->created_at);
            $dt2 = new Carbon($fecha_solicitada);
            $dias = $dt->diffInDays($dt2);
        }
        if($dias > 45){
            return false;
        }
        return true;
    }
}