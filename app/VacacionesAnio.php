<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacacionesAnio extends Model
{
    use SoftDeletes;
    // public $table='vacaciones_anio';
    public $incrementing = false;
}
