<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ocupacion extends Model
{
  use SoftDeletes;
  // public $incrementing = false;
  protected $table = 'ocupaciones';
  protected $guarded = ['id','created_at','updated_at','deleted_at'];


  public function setVigenciaDeAttribute($input)
  {
    if($input !=""){
      $d = explode(' ', $input);
      $d = explode('/', $d[0]);
      $this->attributes['vigencia_de'] =  date('Y-m-d', strtotime($d[0].'-'.$d[1].'-'.$d[2]));
    }
    // $this->attributes['vigencia_de'] = Carbon::createFromFormat('d/m/Y', $input)->format('Y-m-d');
    // $this->attributes['vigencia_de'] =  Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function getVigenciaDeAttribute($input)
{
  if($input !=""){
    $d = explode(' ', $input);
    $d = explode('-', $d[0]);
      // return Carbon::createFromFormat('Y-m-d', $input)->format(config('d/m/Y'));
    return  date('d/m/Y', strtotime($d[0].'-'.$d[1].'-'.$d[2]));
  }
}

//
public function setVigenciaAAttribute($input)
{
  if($input !="" && $input != null){
    $d = explode(' ', $input);
    $d = explode('/', $d[0]);
    $this->attributes['vigencia_a'] =  date('Y-m-d', strtotime($d[2].'-'.$d[1].'-'.$d[0]));
  }
}

public function getVigenciaAAttribute($input)
{
  if($input !=""){
    $d = explode(' ', $input);
    $d = explode('-', $d[0]);
    return  date('d/m/Y', strtotime($d[0].'-'.$d[1].'-'.$d[2]));
  }
}

public function datosLaborales(){
    return $this->hasMany(DatoLaboral::class)->withDefault();
}

}
