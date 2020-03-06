<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AppendPolicies;
use App\Traits\LazyAppends;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;

class Audiencia extends Model
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies;

    /**
     * Nombre de la tabla
     * @var string
     */
    protected $table = 'audiencias';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    protected $loadable = ['conciliador', 'sala','parte','resolucion'];

    /**
     * Relación con expediente
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expediente(){
      return $this->belongsTo('App\Expediente')->withDefault();
    }

    /**
     * Relación con conciliador
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conciliador(){
      return $this->belongsTo('App\Conciliador')->withDefault();
    }

    /**
     * Relación con parte
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parte()
    {
        return $this->belongsTo(Parte::class, 'parte_responsable_id')->withDefault();
    }

    /**
     * Relación con resolución
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resolucion(){
      return $this->belongsTo('App\Resolucion')->withDefault();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comparecientes(){
      return $this->hasMany('App\Compareciente');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salasAudiencias(){
      return $this->hasMany('App\SalaAudiencia');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conciliadoresAudiencias(){
      return $this->hasMany('App\ConciliadorAudiencia');
    }
    /**
     * Relacion con tabla agendas audiencias
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agendasAudiencia(){
      return $this->hasMany(AgendaAudiencia::class);
    }
}
