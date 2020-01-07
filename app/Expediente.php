<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model
{
    use SoftDeletes;
	public function audiencia(){
		return $this->hasMany('App\Audiencia');
	}
}
