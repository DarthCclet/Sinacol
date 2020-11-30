<?php


namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Implementa las consultas a la base de datos correspondientes a usuario y sus relaciones
 * Class UserFilter
 * @package App\Filters
 */
class UserFilter extends Filter
{
    /**
     * Campos por los que es válido ordenar las consultas
     * @var array
     */
    protected $valid_sort_by = [
        'name',
        'email',
        'created_at',
        'updated_at',
    ];


    /**
     * Se permite filtrar por el centro_id
     * @param $centro_id
     */
    public function handleCentroIdFilter($centro_id)
    {
        $this->query->where('centro_id', $centro_id);
    }

    /**
     * Se consulta por abreviatura de centro
     * @param $abreviatura
     */
    public function handleCentroFilter($abreviatura)
    {
        $this->query->whereHas('centro', function (Builder $q) use ($abreviatura) {
            $q->where('abreviatura', '=', $abreviatura);
        });
    }

    /**
     * Se consulta por nombre
     * @param $nombre
     */
    public function handleNombreFilter($nombre)
    {
        $this->query->whereHas('persona', function (Builder $q) use ($nombre) {
            $q->where('nombre', 'ilike', '%'.$nombre.'%');
        });
    }

    /**
     * Se consulta por primer apellido
     * @param $nombre
     */
    public function handlePrimerApellidoFilter($nombre)
    {
        $this->query->whereHas('persona', function (Builder $q) use ($nombre) {
            $q->where('perimer_apellido', 'ilike', '%'.$nombre.'%');
        });
    }

    /**
     * Se consulta por rol
     * @param $nombre
     */
    public function handleRolFilter($nombre)
    {
        $this->query->whereHas('roles', function (Builder $q) use ($nombre) {
            $q->where('name', 'ilike', '%'.$nombre.'%');
        });
    }


}
