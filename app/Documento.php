<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Documento extends Model
{
    protected $table = 'documentos';
    // funcion que indica que es una relación polimorfica
    public function documentable()
    {
        return $this->morphTo();
    }
}
