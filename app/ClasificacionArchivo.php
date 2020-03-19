<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionArchivo extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', ];

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
