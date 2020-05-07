<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudienciaParte extends Model
{
    protected $table = 'audiencias_partes';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /**
     * Relacion con audiencia
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function audiencia(){
      return $this->belongsTo(Audiencia::class);
    }
    /**
     * Relacion con parte
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parte(){
      return $this->belongsTo(Parte::class);
    }
}
