<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conciliador extends Model
{
    protected $table = 'conciliadores';
    public function persona(){
    	return $this->belongsTo(Persona::class); 
    }
    public function centro(){
    	return $this->belongsTo(Centro::class); 
    }
	public function audiencia(){
		return $this->hasMany('App\Audiencia');
	}
}
