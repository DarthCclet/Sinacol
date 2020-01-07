<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Centro extends Model
{
    use SoftDeletes;
    public function solicitudes(){
      return $this->hasMany('App\Solicitud');
    }
}
