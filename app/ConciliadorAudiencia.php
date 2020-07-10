<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Support\Arr;

class ConciliadorAudiencia extends Model implements AuditableContract
{
    use SoftDeletes;
    use Auditable,
    \App\Traits\CambiarEventoAudit;
    protected $table = 'conciliadores_audiencias';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    protected $loadable = ['conciliadores','audiencias'];
    public function transformAudit($data):array
    {
        if (Arr::has($data, 'new_values.conciliador_id')) {
            if($data["event"] != "created"){
                $data['old_values']['conciliador'] = Sala::find($this->getOriginal('conciliador_id'))->sala;
                unset($data['old_values']['conciliador_id']);
            }
            $data['new_values']['conciliador'] = Sala::find($this->getAttribute('conciliador_id'))->sala;
            unset($data['new_values']['conciliador_id']);
        }
        $data = $this->cambiarEvento($data);
        return $data;
    }
    /*
     * Relación con la tabla Salas
     * una sala_audiencia puede tener muchas salas
     */
    public function conciliador(){
        return $this->belongsTo(Conciliador::class);
    }
    /*
     * Relacion con la tabla audiencias
     * una sala_audiencia debe tener muchas audiencias
     */
    public function audiencia(){
        return $this->belongsTo(Audiencia::class);
    }
}
