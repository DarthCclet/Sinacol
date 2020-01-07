<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    protected $table = 'disponibilidades';
    // funcion que indica que es una relación polimorfica
    public function disponibiliable()
    {
        return $this->morphTo();
    }
}
