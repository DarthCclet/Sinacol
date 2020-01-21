<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;
use App\Traits\LazyAppends;
use App\Traits\AppendPolicies;

class Parte extends Model
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies;

        /**
     * Las relaciones que son cargables.
     *
     * @var array
     */
    protected $loadable = ['solicitud', 'tipoParte','genero','tipoPersona','nacionalidad','entidadNacimiento','giroComercial','grupoVulnerable'];
    /**
     * Funcion para asociar con modelo Genero
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Genero(){
      return $this->belongsTo('App\Genero');
    }

    /**
     * Funcion para asociar con modelo Solicitud
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitud(){
      return $this->belongsTo('App\Solicitud');
    }
    /**
     * Funcion para asociar con modelo TipoParte
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoParte(){
      return $this->belongsTo('App\TipoParte');
    }
    /**
     * Funcion para asociar con modelo TipoPersona
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoPersona(){
      return $this->belongsTo('App\TipoPersona');
    }
    /**
     * Funcion para asociar con modelo Nacionalidad
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nacionalidad(){
      return $this->belongsTo('App\Nacionalidad');
    }
    /**
     * Funcion para asociar con modelo Estado
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entidadNacimiento(){
      return $this->belongsTo('App\Estado');
    }

    /**
     * Funcion para asociar con modelo Estado
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function giroComercial(){
      return $this->belongsTo('App\GiroComercial');
    }

    /**
     * Funcion para asociar con modelo Estado
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupoVulnerable(){
      return $this->belongsTo('App\GrupoVulnerable');
    }
    /**
     * Funcion para asociar con modelo Audiencia con hasMany
     * * Utilizando hasMany para relacion uno a muchos
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function audiencias(){
		return $this->hasMany('App\Audiencia');
	}
}
