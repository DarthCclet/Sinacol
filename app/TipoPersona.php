<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoPersona extends Model
{
    //
    use SoftDeletes;
    protected $softDelete = true;
	public function personas(){
      return $this->hasMany('App\Persona');
    }
}
