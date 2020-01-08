<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Disponibilidad extends Model
{
    use SoftDeletes;
    protected $table = 'disponibilidades';
    /*
     *  funcion que indica que es una relación polimorfica
     *  Disponibilidad puede ser usado por Conciliadores, Salas y centros
     */
    public function disponible()
    {
        return $this->morphTo();
    }
}
