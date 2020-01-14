<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resolucion extends Model
{
    use SoftDeletes;
    protected $table = 'resoluciones';
    protected $fillable = ['resolucion'];
    /*
     * Relacion con la tabla audiencias
     * una audiencia debe tener resolución
     */
    public function audiencias(){
        return $this->hasMany(Audiencia::class);
    }
}
