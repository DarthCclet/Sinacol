<?php


namespace App\Filters;

/**
 * Métodos para filtrar consultas de Audiencia
 * @package App\Filters
 */
class AudienciaFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'numero_audiencia'
    ];

    /**
     * Cuando se pasa el centro_id como parametro
     * @param array|integer $centro_id Arreglo de IDs de centros o un sólo ID de centro
     */
    public function handleCentroIdFilter($centro_id)
    {
        if(is_array($centro_id)){
            $this->query->whereIn('centro_id', $centro_id);
        }else{
            if(!trim($centro_id)) return;
            $this->query->where('centro_id', $centro_id);
        }
    }

    /**
     * Cuando se pasa la abreviatura del centro como parametro
     * @param array|string $abreviatura Arreglo de abreviaturas o abreviatura del nombre del centro
     */
    public function handleCentroFilter($abreviatura)
    {
        if (is_array($abreviatura)) {
            $this->query->whereIn('centros.abreviatura', $abreviatura);
        } else {
            if (!trim($abreviatura)) {
                return;
            }
            $this->query->where('centros.abreviatura', $abreviatura);
        }

    }

    /**
     * Cuando se pasa como parámetro el objeto_id de la solicitud
     * @param integer $objeto_id ID del objeto de la solicitud
     */
    public function handleObjetoIdFilter($objeto_id)
    {
        //$this->query->where('centros.abreviatura', $abreviatura);
    }

    /**
     * Cuando se pasa como parámetro el objeto de la solicitud
     * @param string $objeto Nombre del objeto de la solicitud
     */
    public function handleObjetoFilter($objeto)
    {
        //$this->query->where('centros.abreviatura', $abreviatura);
    }


    /**
     * Cuando filtran por conciliadores
     * @param array|integer $conciliadores Arreglo de ID de conciliadores o un sólo ID de conciliador
     */
    public function handleConciliadoresFilter($conciliadores)
    {
        if (is_array($conciliadores)) {
            $this->query->whereIn('audiencias.conciliador_id', $conciliadores);
        } else {
            if (!trim($conciliadores)) {
                return;
            }
            $this->query->where('audiencias.conciliador_id', $conciliadores);
        }
    }
}
