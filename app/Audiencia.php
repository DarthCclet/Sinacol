<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audiencia extends Model
{
    //relación con expediente
	public function expediente(){
      return $this->belongsTo('App\Expediente');
    }
	//relación con conciliador
	public function conciliador(){
      return $this->belongsTo('App\Conciliador');
    }
	//relación con sala
	public function sala(){
      return $this->belongsTo('App\Sala');
    }
	//relación con parte
	public function parte(){
      return $this->belongsTo('App\Parte');
    }
	//relación con resolucion
	public function resolucion(){
      return $this->belongsTo('App\Resolucion');
    }
	//relación con compareciente
	public function compareciente(){
      return $this->hasMany('App\Compareciente');
    }
}
