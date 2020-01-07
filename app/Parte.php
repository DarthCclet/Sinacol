<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parte extends Model
{
    //
    use SoftDeletes;
    public function genero(){
      return $this->belongsTo('App\Genero');
    }
    public function solicitud(){
      return $this->belongsTo('App\Solicitud');
    }
    public function tipo_parte(){
      return $this->belongsTo('App\TipoParte');
    }
    public function tipo_persona(){
      return $this->belongsTo('App\TipoPersona');
    }
    public function nacionalidad(){
      return $this->belongsTo('App\Nacionalidad');
    }
    public function entidad_nacimiento(){
      return $this->belongsTo('App\Estado');
    }
	public function audiencia(){
		return $this->hasMany('App\Audiencia');
	}
}
