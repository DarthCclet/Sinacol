<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abogado extends Model
{
    use SoftDeletes;
    protected $guarded = ['id','updated_at','created_at']; 
    /**
     * Funcion para asociar con modelo Solicitud con hasMany
     * * Utilizando hasMany para relacion uno a muchos
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solicitudes(){
      return $this->hasMany('App\Solicitud');
    }
}
