<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoricoNotificacionPeticion extends Model
{
    protected $table = "historico_notificaciones_peticiones";
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    public function historico_notificacion(){
        return $this->belongsTo(HistoricoNotificacion::class,'historico_notificacion_id');
    }
    public function historico_notificacion_respuesta(){
        return $this->belongsTo(HistoricoNotificacionRespuesta::class,'historico_notificacion_respuesta_id');
    }
}
