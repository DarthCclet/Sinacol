<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResolucionPagoDiferido extends Model
{
    protected $table = 'resolucion_pagos_diferidos';
    protected $guarded = ['id','created_at','updated_at'];
}
