<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResolucionParteConcepto extends Model
{
    protected $table = 'resolucion_parte_conceptos';
    protected $guarded = ['id','created_at','updated_at'];
    
    /*
     * Relación con la tabla conceptoPagoResolucion
     * un centro puede tener muchas salas
     */
    public function ConceptoPagoResolucion(){
        return $this->belongsTo(ConceptoPagoResolucion::class);
    }
    /*
     * Relación con la tabla resolucionPartes
     * un centro puede tener muchas salas
     */
    public function ResolucionPartes(){
        return $this->belongsTo(ResolucionPartes::class);
    }
}
