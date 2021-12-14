<?php

namespace App\Filters;

use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Métodos para filtrar consultas mediante la petición HTTP
 * @package App\Filters
 */
class Filter
{
    /**
     * @var
     */
    protected $query;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string Nombre de la tabla
     */
    protected $tableName;

    /**
     * @var array Arreglo por el que se puede ordenar la consulta
     */
    protected $valid_sort_by = [
        'created_at',
    ];

    /**
     * Filter constructor.
     * @param Builder $query Métodos para construir consultas al modelo en la Base de Datos.
     * @param Request $request Métodos para obtener parámetros de la petición HTTP
     */
    public function __construct($query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    /**
     * Agrega los filtros al constructor de consultas
     * @param bool $ordered Indica si se aplica el ordenado al filtro o no.
     * @return Builder
     */
    public function filter($ordered = true)
    {
        $params = collect($this->request->all());
        $params->each(function ($value, $key){
            $methodName = Str::camel(sprintf('handle_%s_filter', $key));
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        });

        if($this->query->getModel()->timestamps && $ordered){
            return $this->query->latest($this->getTable() . '.created_at');
        }
        return $this->query;
    }

    /**
     * Agrega las relaciones que se deben cargar como eager load
     * @param $scoutModelClass
     * @return $this
     */
    public function searchWith($scoutModelClass)
    {
        $model = new $scoutModelClass;
        if ($search = $this->request->input('search')) {
            //TODO: Implementar cuando el parametro exista con un valor buscable
            //para esto tendrá que implementarse Scout con postgres inicialmente y luego ver una buena máquina de indexado y fulltextsearch
            if(is_array($search) && array_key_exists('value', $search)) {
                return $this;
            }
            $ids = $model::search($search)->get()->pluck('id');
            $table = $this->getTable();
            if ($ids->count()) {
                $join = sprintf('(select * from unnest(array[%s]) with ordinality) as o (id, ordering)', $ids->join(','));
                $this->query->join(DB::raw($join), $table . '.id', '=', 'o.id');
                $this->query->orderByRaw('o.ordering');
            } else {
                $this->query->where($table . '.id', -1);
            }
        }

        return $this;
    }

    /**
     * Agrega al constructor de consultas el ordenado de campos pasados como parametro
     * @param array $sortBy Campos por los que se debe ordenar la consulta
     */
    protected function handleSortByFilter($sortBy)
    {
        if ($this->request->input('dir') !== null && in_array($sortBy, $this->valid_sort_by)) {
            $sortDirection = $this->request->input('dir', 'asc');
            if (!str_contains($sortBy, '.')) {
                $sortBy = $this->getTable() . '.' . $sortBy;
            }
            $this->query->orderBy($sortBy, $sortDirection);
        }
    }

    /**
     * Obtiene el nombre de la tabla en la BD que se va a consultar
     * @return string
     */
    protected function getTable() {
        if ($this->tableName) {
            return $this->tableName;
        }

        return $this->tableName = $this->query->getModel()->getTable();
    }
}
