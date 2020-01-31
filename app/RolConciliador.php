<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolConciliador extends Model
{
    use SoftDeletes;
    protected $table = 'rol_conciliadores';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    // public $incrementing = false;
    //
    /**
     * relaci�n con la tabla conciliadores
     * @return type
     */
    public function rolConciliador(){
    	return $this->hasMany(RolConciliador::class);
    }
}
