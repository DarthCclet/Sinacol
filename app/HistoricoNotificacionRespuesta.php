<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoricoNotificacionRespuesta extends Model
{
    protected $table = "historico_notificaciones_respuestas";
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    public function histrorico(){
        return $this->belongsTo(HistoricoNotificacion::class);
    }
    public function etapa_notificacion(){
        return $this->belongsTo(EtapaNotificacion::class);
    }
    public function documento(){
        return $this->belongsTo(Documento::class);
    }
}
