<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AppendPolicies;
use App\Traits\LazyAppends;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;

class Sala extends Model
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies; 
    protected $table = 'salas';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    protected $loadable = ['centro'];
    /*
     * Relación con la tabla Centros
     * un centro puede tener muchas salas
     */
    public function centro(){
        return $this->belongsTo(Centro::class);
    }
    /*
     * Relacion con la tabla audiencias
     * una audiencia debe tener una sala
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
     * Relacion con tabla agendas audiencias
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agendasAudiencia(){
      return $this->hasMany(AgendaAudiencia::class);
    }
}
