<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoParte extends Model
{
    use SoftDeletes;
    public $incrementing = false;
}
