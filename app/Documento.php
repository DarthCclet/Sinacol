<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Documento extends Model
{
    use SoftDeletes;
    protected $table = 'documentos';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /*
     *  funcion que indica que es una relación polimorfica
     *  Documentable puede ser usada por toda tabla que requiera subir documentos
     */
    public function documentable()
    {
        return $this->morphTo();
    }
}
