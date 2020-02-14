<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConciliadorAudiencia extends Model
{
    use SoftDeletes; 
    protected $table = 'conciliadores_audiencias';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    protected $loadable = ['conciliadores','audiencias'];
    /*
     * Relación con la tabla Salas
     * una sala_audiencia puede tener muchas salas
     */
    public function conciliador(){
        return $this->belongsTo(Sala::class);
    }
    /*
     * Relacion con la tabla audiencias
     * una sala_audiencia debe tener muchas audiencias
     */
    public function audiencia(){
        return $this->belongsTo(Audiencia::class);
    }
}
