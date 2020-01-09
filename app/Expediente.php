<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model
{
    use SoftDeletes;
    /**
     * Funcion para asociar con modelo Audiencia
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function audiencia(){
		return $this->hasMany('App\Audiencia');
	}

  /**
   * Funcion para asociar con modelo Solicitud
   * Utilizando belongsTo para relaciones 1 a 1
   * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function solicitud(){
    return $this->belongsTo('App\Solicitud');
  }
}
