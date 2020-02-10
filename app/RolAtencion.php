<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolAtencion extends Model
{
    protected $table = 'roles_atencion';
    protected $guarded = ['id','created_at','updated_at','deleted_at'];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function RolesConciliador(){
      return $this->hasMany('App\RolConciliador');
    }
}
