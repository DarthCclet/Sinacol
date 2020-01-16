<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conciliador extends Model
{
    use SoftDeletes;
    protected $table = 'conciliadores';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /*
     * Relacion con ta tabla de personas
     * una conciliador debe tener una persona
     */
    public function persona(){
    	return $this->belongsTo(Persona::class); 
    }
    /*
     * Relacion con la tabla de centros
     * una conciliador debe tener un centro
     */
    public function centro(){
    	return $this->belongsTo(Centro::class); 
    }
    /**
     * relación con la tabla rol_conciliador
     * @return type
     */
    public function rolConciliador(){
    	return $this->belongsTo(RolConciliador::class); 
    }
    /*
     * Relacion con la tabla de audiencias
     * una conciliador puede tener muchas audiencias
     */
    public function audiencias(){
        return $this->hasMany('App\Audiencia');
    }
}
