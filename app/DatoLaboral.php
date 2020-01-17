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

    //
}
