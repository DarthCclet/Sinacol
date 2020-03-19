<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoContador extends Model
{
    use SoftDeletes;
    public $incrementing = false;
    public function contador(){
        return $this->hasMany(Contador::class);
    }
}
