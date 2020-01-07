<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centro extends Model
{

    use SoftDeletes;

    /**
     * Funcion para asociar con modelo Solicitud con hasMany
     * * Utilizando hasMany para relacion uno a muchos
     */
    public function solicitudes(){
      return $this->hasMany('App\Solicitud');
    }

    /**
     * Funcion para asociar con modelo Salas con hasMany
     * * Utilizando hasMany para relacion uno a muchos
     */
	public function salas(){
      return $this->hasMany('App\Sala');
    }

  /**
   * Funcion para asociar con modelo Solicitud con hasMany
   * * Utilizando hasMany para relacion uno a muchos
   */
	public function conciliadores(){
		return $this->hasMany('App\Conciliador');
	}

}
