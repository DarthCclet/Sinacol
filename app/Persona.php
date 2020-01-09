<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;
    protected $table='personas';
    /*
     * Relacion con la tabla tipo personas
     * una persona debe tener un tipo persona
     */
    public function tipoPersona(){
    	return $this->belongsTo(TipoPersona::class);
    }
    /*
     * Relación inversa con la tabla conciliadores
     * una persona puede ser un conciliador
     */
    public function conciliadores(){
    	return $this->hasMany(Conciliador::class);
    }
}
