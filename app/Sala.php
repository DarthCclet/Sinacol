<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    protected $table = 'salas';
    public function centro()
    {
        return $this->belongsTo(Centro::class);
    }
	public function audiencia(){
		return $this->hasMany('App\Audiencia');
	}
}
