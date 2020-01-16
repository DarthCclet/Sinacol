<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resolucion extends Model
{
    use SoftDeletes;
    protected $table = 'resoluciones';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /*
     * Relacion con la tabla audiencias
     * una audiencia debe tener resolución
     */
    public function audiencias(){
        return $this->hasMany(Audiencia::class);
    }
}
