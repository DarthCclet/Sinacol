<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetoSolicitud extends Model
{
    use SoftDeletes;
    // public $incrementing = false;
    protected $table = 'objeto_solicitudes';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
}
