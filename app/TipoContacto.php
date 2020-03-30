<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoContacto extends Model
{
    use SoftDeletes;
    public function contacto(){
        return $this->hasMany(Contacto::class);
    }
}
