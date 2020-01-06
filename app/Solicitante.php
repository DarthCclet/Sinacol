<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitante extends Model
{
    //
    use SoftDeletes;
    protected $softDelete = true;
    public function genero(){
      return $this->belongsTo('App\Genero');
    }
}

/*menor de edad
adulto mayor
mujeres enbarazadas*/
