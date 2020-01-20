<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Incidencia extends Model
{
    use SoftDeletes;
    protected $table = 'incidencias';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /*
     *  funcion que indica que es una relación polimorfica
     *  incidenciable puede ser usado por Conciliadores, Salas y centros
     */
    public function incidenciable()
    {
        return $this->morphTo();
    }
}
