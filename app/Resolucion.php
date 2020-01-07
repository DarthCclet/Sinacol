<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resolucion extends Model
{
    protected $table = 'resoluciones';
	public function audiencia(){
		return $this->hasMany(Audiencia::class);
	}
}
