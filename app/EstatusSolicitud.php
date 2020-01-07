<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstatusSolicitud extends Model
{
  use SoftDeletes;
  protected $table = 'estatus_solicitudes';
  public function solicitudes(){
    return $this->hasMany('App\Solicitud');
  }
}
