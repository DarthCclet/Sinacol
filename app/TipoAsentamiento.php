<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoAsentamiento extends Model
{
    /**
     * No queremos que autoincremente el id, los cambios en este catálogo deberán ser tratados manualmente
     * y de forma idéntica a lo que tengan las fuentes de estos catálogos.
     */
    public $incrementing = false;
}
