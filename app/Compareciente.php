<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compareciente extends Model
{
    //
	public function audiencia(){
		return $this->belongsTo('App\Audiencia');
	}
}
