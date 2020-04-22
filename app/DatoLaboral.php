<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatoLaboral extends Model
{
  use SoftDeletes;
  protected $table = 'datos_laborales';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id', 'created_at', 'updated_at'];

/**
 * asocia datos_laborales con la tabla de jornada
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
  public function jornada(){
    return $this->belongsTo('App\Jornada');

  }

/**
 * Funcion para asociar con modelo Estado
 * Utilizando belongsTo para relaciones 1 a 1
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function giroComercial(){
  return $this->belongsTo('App\GiroComercial');
}

  /**
 * asocia datos_laborales con la tabla de parte
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function parte(){
  return $this->belongsTo('App\Parte');

}
  /**
 * asocia oficios con la tabla de parte
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function ocupacion(){
  return $this->belongsTo(Ocupacion::class);

}

    //
}
