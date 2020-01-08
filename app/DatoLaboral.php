<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatoLaboral extends Model
{
  use SoftDeletes;
  protected $table = 'datos_laborales';

/**
 * asocia datos_laborales con la tabla de jornada
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
  public function jornada(){
    return $this->belongsTo('App\Jornada');

  }

    //
}
