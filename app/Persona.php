<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Persona
 * @package App
 */
class Persona extends Model
{
    use SoftDeletes;
    protected $table='personas';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    /**
     * Una persona puede tener una cuenta de usuario del sistema.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Relacion con la tabla tipo personas
     * una persona debe tener un tipo persona
     */
    public function tipoPersona(){
    	return $this->belongsTo(TipoPersona::class);
    }

    /**
     * Relación inversa con la tabla conciliadores
     * una persona puede ser un conciliador
     */
    public function conciliadores(){
    	return $this->hasMany(Conciliador::class);
    }
}
