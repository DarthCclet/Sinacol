<?php


namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Métodos para filtrar consultas mediante la petición HTTP de Usuario
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
     * @param integer $centro_id El ID del centro
     */
    public function handleCentroIdFilter($centro_id)
    {
        $this->query->where('centro_id', $centro_id);
    }

    /**
     * Se consulta por abreviatura de centro
     * @param string $abreviatura La abreviatura del centro
     */
    public function handleCentroFilter($abreviatura)
    {
        $this->query->whereHas('centro', function (Builder $q) use ($abreviatura) {
            $q->where('abreviatura', '=', $abreviatura);
        });
    }

    /**
     * Se consulta por nombre
     * @param string $nombre El nombre de la persona o un fragmento del nombre
     */
    public function handleNombreFilter($nombre)
    {
        $this->query->whereHas('persona', function (Builder $q) use ($nombre) {
            $q->where('nombre', 'ilike', '%'.$nombre.'%');
        });
    }

    /**
     * Se consulta por primer apellido
     * @param string $nombre El primer apellido completo o un fragmento
     */
    public function handlePrimerApellidoFilter($nombre)
    {
        $this->query->whereHas('persona', function (Builder $q) use ($nombre) {
            $q->where('perimer_apellido', 'ilike', '%'.$nombre.'%');
        });
    }

    /**
     * Se consulta por rol
     * @param string $nombre El nombre del rol completo o un fragmento del nombre
     */
    public function handleRolFilter($nombre)
    {
        $this->query->whereHas('roles', function (Builder $q) use ($nombre) {
            $q->where('name', 'ilike', '%'.$nombre.'%');
        });
    }


}
