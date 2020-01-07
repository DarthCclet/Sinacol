<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use SoftDeletes;
    protected $table = 'solicitudes';
    public function abogado(){
      return $this->belongsTo('App\Abogado');
    }
    public function estatus_solicitud(){
      return $this->belongsTo('App\EstatusSolicitud');
    }
    public function motivo_solicitud(){
      return $this->belongsTo('App\MotivoSolicitud');
    }
    public function centro(){
      return $this->belongsTo('App\Centro');
    }
    public function user(){
      return $this->belongsTo('App\User');
    }
}
