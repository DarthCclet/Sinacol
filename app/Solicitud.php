<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use SoftDeletes;
    protected $table = 'solicitudes';

    /**
     * Funcion para asociar con modelo Abogado con belongsTo
     */
    public function abogado(){
      return $this->belongsTo('App\Abogado');
    }
    /**
     * Funcion para asociar con modelo EstatusSolicitud
     * Utilizando belongsTo para relaciones 1 a 1
     */
    public function estatusSolicitud(){
      return $this->belongsTo('App\EstatusSolicitud');
    }
    /**
     * Funcion para asociar con modelo MotivoSolicitud con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     */
    public function motivoSolicitud(){
      return $this->belongsTo('App\MotivoSolicitud');
    }

    /**
     * Funcion para asociar con modelo Centro con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     */
    public function centro(){
      return $this->belongsTo('App\Centro');
    }

    /**
     * Funcion para asociar con modelo User con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     */
    public function user(){
      return $this->belongsTo('App\User');
    }
}
