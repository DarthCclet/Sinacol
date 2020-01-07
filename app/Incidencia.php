<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'incidencias';
    // funcion que indica que es una relación polimorfica
    public function incidenciable()
    {
        return $this->morphTo();
    }
}
