<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoricoNotificacion extends Model
{
    protected $table = "historico_notificaciones";
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    public function respuestas(){
        return $this->hasMany(HistoricoNotificacionRespuesta::class);
    }
}
