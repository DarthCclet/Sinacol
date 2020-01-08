<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audiencia extends Model
{
    use SoftDeletes;
    protected $table = 'audiencias';
    /*
     * relación con expediente
     */
    public function expediente(){
      return $this->belongsTo('App\Expediente');
    }
    //relación con conciliador
    public function conciliador(){
      return $this->belongsTo('App\Conciliador');
    }
    //relación con sala
    public function sala(){
      return $this->belongsTo('App\Sala');
    }
    //relación con parte
    public function parte(){
      return $this->belongsTo('App\Parte');
    }
    //relación con resolucion
    public function resolucion(){
      return $this->belongsTo('App\Resolucion');
    }
    //relación con compareciente
    public function comparecientes(){
      return $this->hasMany('App\Compareciente');
    }
}
