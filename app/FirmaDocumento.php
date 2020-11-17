<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirmaDocumento extends Model
{
    use SoftDeletes;
    protected $table = 'firmas_documentos';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
}
