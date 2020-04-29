<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compareciente extends Model
{
    use SoftDeletes;
    protected $table = 'comparecientes';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /*
     * Funcion de la relación con la tabla de audiencias
     * una audiencia tiene varios comparecientes
     */
    public function audiencia(){
        return $this->belongsTo('App\Audiencia');
    }
    public function parte(){
        return $this->belongsTo('App\Parte');
    }
}
