<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetoCita extends Model
{
    use SoftDeletes;
    public $incrementing = false;
}
