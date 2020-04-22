<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AppendPolicies;
use App\Traits\LazyAppends;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;

class Conciliador extends Model
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies; 
    protected $table = 'conciliadores';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    protected $loadable = ['persona'];
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rolesConciliador(){
      return $this->hasMany('App\RolConciliador');
    }
    /*
     * Relacion con la tabla de audiencias
     * una conciliador puede tener muchas audiencias
     */
    public function audiencias(){
        return $this->hasMany('App\Audiencia');
    }
    /**
     * Relacion con la tabla disponibilidad
     * @return type
     */
    public function disponibilidades(){
        return $this->morphMany(Disponibilidad::class,'disponibilidad');
    }
    /**
     * Relacion con la tabla incidencias
     * @return type
     */
    public function incidencias(){
        return $this->morphMany(Incidencia::class,'incidenciable');
    }
    /**
     * Relacion con la tabla conciliadoresAudiencia
     * @return type
     */
    public function conciliadorAudiencia(){
        return $this->hasMany(ConciliadorAudiencia::class,'incidenciable');
    }
}
