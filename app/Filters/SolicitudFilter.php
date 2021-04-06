<?php


namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Implementa las consultas a la base de datos correspondientes a usuario y sus relaciones
 * Class UserFilter
 * @package App\Filters
 */
class SolicitudFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'fecha_ratificacion',
        'centro_id',
        'created_at',
        'updated_at',
    ];


    /**
     * Cuando se pasa el centro_id como parametro
     * @param $centro_id
     */
    public function handleCentroIdFilter($centro_id)
    {
        if(!trim($centro_id)) return;
        $this->query->where('centro_id', $centro_id);
    }

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param $abreviatura
     */
    public function handleCentroFilter($abreviatura)
    {
        if(!trim($abreviatura)) return;
        $this->query->where('centros.abreviatura', strtoupper($abreviatura));
    }

    /**
     * Cuando se pasa como parámetro objeto_solicitud_id
     * @param $objeto_solicitud_id
     */
    public function handleObjetoSolicitudIdFilter($objeto_solicitud_id)
    {
        if(!trim($objeto_solicitud_id)) return;
        $this->query->whereHas('objeto_solicitudes', function($q) use($objeto_solicitud_id){
            $q->where('objeto_solicitud_id', $objeto_solicitud_id);
        });
    }
}
