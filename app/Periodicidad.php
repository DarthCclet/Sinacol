<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periodicidad extends Model
{
    use SoftDeletes;
    protected $table = 'periodicidades';
}
