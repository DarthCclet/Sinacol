<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contador extends Model
{
    use SoftDeletes;
    protected $table='contadores';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /**
     * Relacion con la tabla tipo_contadores
     * un contador debe tener un tipo contador
     */
    public function tipoPersona(){
    	return $this->belongsTo(TipoContador::class);
    }
    
}
