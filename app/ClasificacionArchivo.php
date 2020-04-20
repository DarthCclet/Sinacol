<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClasificacionArchivo extends Model
{
    use SoftDeletes;
    protected $guarded = ['id', 'created_at', 'updated_at', ];

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
