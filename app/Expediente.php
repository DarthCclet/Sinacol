<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\ValidTypes;

class Expediente extends Model implements Auditable
{
    use SoftDeletes,
    \OwenIt\Auditing\Auditable,
    ValidTypes,
    \App\Traits\CambiarEventoAudit;
    protected $guarded = ['id','updated_at','created_at'];
    public function transformAudit($data):array
    {
        $data = $this->cambiarEvento($data);
        return $data;
    }
    /**
     * Funcion para asociar con modelo Audiencia
     * Utilizando belongsTo para relaciones 1 a 1
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function audiencia(){
		return $this->hasMany('App\Audiencia');
	}

  /**
   * Funcion para asociar con modelo Solicitud
   * Utilizando belongsTo para relaciones 1 a 1
   * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function solicitud(){
    return $this->belongsTo('App\Solicitud');
  }
}
