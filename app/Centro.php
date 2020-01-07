<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centro extends Model
{
    use SoftDeletes;
    public function solicitudes(){
      return $this->hasMany('App\Solicitud');
    }
	public function sala(){
      return $this->hasMany('App\Sala');
    }
	public function conciliador(){
		return $this->hasMany('App\Conciliador');
	}
}
