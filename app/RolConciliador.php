<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolConciliador extends Model
{
    use SoftDeletes;
    protected $table = 'rol_conciliadores';
    //
}
