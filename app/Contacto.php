<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contacto extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', ];
    /**
     * Declara la entidad como polimorfica
     * @return MorphTo
     */
    public function contactable()
    {
        return $this->morphTo();
    }
}
