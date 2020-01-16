<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sala extends Model
{
   use SoftDeletes; 
    protected $table = 'salas';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /*
     * Relación con la tabla Centros
     * un centro puede tener muchas salas
     */
    public function centro(){
        return $this->belongsTo(Centro::class);
    }
    /*
     * Relacion con la tabla audiencias
     * una audiencia debe tener una sala
     */
    public function audiencias(){
        return $this->hasMany('App\Audiencia');
    }
}
