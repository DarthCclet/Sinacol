<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compareciente extends Model
{
    use SoftDeletes;
    protected $table = 'comparecientes';
    /*
     * Funcion de la relación con la tabla de audiencias
     * una audiencia tiene varios comparecientes
     */
    public function audiencia(){
        return $this->belongsTo('App\Audiencia');
    }
}
