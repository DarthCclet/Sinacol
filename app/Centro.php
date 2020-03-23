<?php

namespace App;

use App\Traits\Domiciliable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centro extends Model
{

    use SoftDeletes, Domiciliable;
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
   * Funcion para asociar con modelo Solicitud con hasMany
   * * Utilizando hasMany para relacion uno a muchos
   * * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
    public function contadores(){
            return $this->hasMany(Contador::class);
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function domicilio()
    {
        return $this->morphTo(Domicilio::class, 'domiciliable');
    }
}
