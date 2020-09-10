<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Carbon\Carbon;

class Incidencia extends Model implements AuditableContract
{
    use SoftDeletes,
        Auditable,
        \App\Traits\CambiarEventoAudit;
    protected $table = 'incidencias';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    public function transformAudit($data):array
    {
        $data = $this->cambiarEvento($data);
        return $data;
    }
    /*
     *  funcion que indica que es una relación polimorfica
     *  incidenciable puede ser usado por Conciliadores, Salas y centros
     */
    public function incidenciable()
    {
        return $this->morphTo();
    }
    
    
    
    
    /**
     * Regresa si es hay incidencia o no
     * @param $fecha timestamp
     * @return bool
     * @throws \Exception
     */
    public static function hayIncidencia($fecha,$id,$incidencia_type)
    {
        $d = new Carbon($fecha);
        if($d->isWeekend()){
            return true;
        }
        $fechaInicioEv = $fecha." 00:00:00";
        $incidencia = self::whereDate("fecha_inicio","<=",$fechaInicioEv)->whereDate("fecha_fin",">=",$fechaInicioEv)->where("incidenciable_type",$incidencia_type)->where("incidenciable_id",$id)->get();
        if(count($incidencia) > 0){
            return true;
        }else{
            $numero_dia = $d->weekday();
            $pasa = true;
            switch ($incidencia_type){
                case "App\Centro":
                    $disponibilidades = Centro::find($id)->disponibilidades()->get();
                break;
                case "App\Sala":
                    $disponibilidades = Sala::find($id)->disponibilidades()->get();
                break;
            }
            foreach($disponibilidades as $disponibilidad){
                if($disponibilidad->dia == $numero_dia){
                    $pasa = false;
                }
            }
            return $pasa;
        }
    }

    /**
     * Regresa el ultimo día hábil desde la fecha dada como referencia.
     * @param $fecha
     * @return mixed
     * @throws \Exception
     */
    public static function ultimoDiaHabilDesde($fecha){

        $d = new Carbon($fecha);
        $ayer = $d->subDay()->format("Y-m-d H:i:s");

        if(self::hayIncidencia($ayer)){
            $d = new Carbon($fecha);
            $ayer = $d->subDay()->format("Y-m-d H:i:s");
            return self::ultimoDiaHabilDesde($ayer);
        }
        else{
            return $ayer;
        }
    }

    /**
     * @param $fecha
     * @return mixed|string|void
     * @throws \Exception
     */
    public static function siguienteDiaHabil($fecha,$id,$incidencia_type)
    {
        $d = new Carbon($fecha);
        $fecha = $d->addDay()->format("Y-m-d");
        if(self::hayIncidencia($fecha,$id,$incidencia_type)){
            $d = new Carbon($fecha);
            $maniana = $d->format("Y-m-d");
            return self::siguienteDiaHabil($maniana,$id,$incidencia_type);
        }
        else{
            return $fecha;
        }
    }

    public static function siguienteDiaHabilMasDias($fecha,$id,$incidencia_type, $dias = 3)
    {
        $d = new Carbon($fecha);
        $fecha = $d->addDays($dias)->format("Y-m-d");
        return self::siguienteDiaHabil($fecha,$id,$incidencia_type);
    }
}
