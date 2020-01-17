<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoVulnerable extends Model
{
    use SoftDeletes;
    protected $table = 'grupos_vulnerables';
    public $incrementing = false;
}
