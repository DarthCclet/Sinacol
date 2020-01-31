<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AppendPolicies;
use App\Traits\LazyAppends;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;

class Solicitud extends Model
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies;
    protected $table = 'solicitudes';
    protected $guarded = ['id','updated_at','created_at']; 

    /**
     * Las relaciones que son cargables.
     *
     * @var array
     */
    protected $loadable = ['abogado', 'estatusSolicitud','motivoSolicitud','centro','user'];

    /**
     * Funcion para asociar con modelo Abogado con belongsTo
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function abogado(){
      return $this->belongsTo('App\Abogado');
    }
    /**
     * Funcion para asociar con modelo EstatusSolicitud
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estatusSolicitud(){
      return $this->belongsTo('App\EstatusSolicitud');
    }
    /**
     * Funcion para asociar con modelo MotivoSolicitud con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function motivoSolicitud(){
      return $this->belongsTo('App\MotivoSolicitud');
    }

    /**
     * Funcion para asociar con modelo Centro con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function centro(){
      return $this->belongsTo('App\Centro');
    }

    /**
     * Funcion para asociar con modelo User con belongsTo
     * * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
      return $this->belongsTo('App\User');
    }

    public function partes()
    {
        return $this->hasMany('App\Parte', 'solicitud_id', 'id');
    }
}
