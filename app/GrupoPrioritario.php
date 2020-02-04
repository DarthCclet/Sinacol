<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoPrioritario extends Model
{
    use SoftDeletes;
    protected $table = 'grupos_prioritarios';
    // public $incrementing = false;
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
}
