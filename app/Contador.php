<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AppendPolicies;
use App\Traits\LazyAppends;
use App\Traits\LazyLoads;
use App\Traits\RequestsAppends;

class Contador extends Model
{
    use SoftDeletes,
        LazyLoads,
        LazyAppends,
        RequestsAppends,
        AppendPolicies;
    protected $table='contadores';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /**
     * Relacion con la tabla tipo_contadores
     * un contador debe tener un tipo contador
     */
    public function tipoContador(){
    	return $this->belongsTo(TipoContador::class);
    }
    /**
     * Relacion con la tabla centros
     * @return type
     */
    public function centro(){
    	return $this->belongsTo(Centro::class);
    }
    
}
