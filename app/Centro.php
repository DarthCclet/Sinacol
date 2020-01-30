<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centro extends Model
{

    use SoftDeletes;
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    /**
     * Funcion para asociar con modelo Solicitud con hasMany
     * * Utilizando hasMany para relacion uno a muchos
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solicitudes(){
      return $this->hasMany('App\Solicitud');
    }

    /**
     * Funcion para asociar con modelo Salas con hasMany
     * * Utilizando hasMany para relacion uno a muchos
     * * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function salas(){
      return $this->hasMany('App\Sala');
    }

  /**
   * Funcion para asociar con modelo Solicitud con hasMany
   * * Utilizando hasMany para relacion uno a muchos
   * * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
    public function conciliadores(){
            return $this->hasMany('App\Conciliador');
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

}
