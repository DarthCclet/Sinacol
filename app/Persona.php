<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table='personas';
    public function TipoPersona(){
    	return $this->belongsTo(TipoPersona::class);
    }
    public function conciliador(){
    	return $this->hasMany(Conciliador::class);
    }
}
