<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoSolicitud extends Model
{
  use SoftDeletes;
  protected $table = 'motivo_solicitudes';
  public function solicitudes(){
    return $this->hasMany('App\Solicitud');
  }
}
