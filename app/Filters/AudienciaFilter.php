<?php


namespace App\Filters;


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
     * @param $centro_id
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
     * @param $abreviatura
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
     * @param $objeto_id
     */
    public function handleObjetoIdFilter($objeto_id)
    {
        //$this->query->where('centros.abreviatura', $abreviatura);
    }

    /**
     * Cuando se pasa como parámetro el objeto de la solicitud
     * @param $objeto
     */
    public function handleObjetoFilter($objeto)
    {
        //$this->query->where('centros.abreviatura', $abreviatura);
    }


    /**
     * Cuando filtran por conciliadores
     * @param $conciliadores
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
