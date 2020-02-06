<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolConciliador extends Model
{
    use SoftDeletes;
    protected $table = 'roles_conciliadores';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];

    /**
     * Relación con conciliador
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conciliador(){
      return $this->belongsTo('App\Conciliador');
    }
    
    /**
     * Relación con roles_atencion
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rolAtencion(){
      return $this->belongsTo('App\RolAtencion');
    }
}
