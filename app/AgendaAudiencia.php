<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaAudiencia extends Model
{
    //
    protected $table = 'agenda_audiencias';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    /**
     * Relación con audiencias
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function audiencia(){
      return $this->belongsTo(Audiencia::class);
    }
    /**
     * Relación con conciliadores
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conciliador(){
      return $this->belongsTo(Conciliador::class);
    }
    /**
     * Relación con salas
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sala(){
      return $this->belongsTo(Sala::class);
    }
}
