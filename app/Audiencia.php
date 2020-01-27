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
      return $this->belongsTo('App\Expediente');
    }

    /**
     * Relación con conciliador
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conciliador(){
      return $this->belongsTo('App\Conciliador');
    }

    /**
     * Relación con sala
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sala(){
      return $this->belongsTo('App\Sala');
    }

    /**
     * Relación con parte
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parte()
    {
        return $this->belongsTo(Parte::class, 'parte_responsable_id');
    }

    /**
     * Relación con resolución
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resolucion(){
      return $this->belongsTo('App\Resolucion');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comparecientes(){
      return $this->hasMany('App\Compareciente');
    }
}
