<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table='personas';
    public function tipoPersona(){
    	return $this->belongsTo(TipoPersona::class);
    }
    public function conciliadores(){
    	return $this->hasMany(Conciliador::class);
    }
}
