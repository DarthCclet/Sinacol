<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Filter
{
    protected $query;
    protected $request;
    protected $tableName;

    protected $valid_sort_by = [
        'created_at',
    ];

    public function __construct($query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    public function filter()
    {
        $params = collect($this->request->all());
        $params->each(function ($value, $key){
            $methodName = Str::camel(sprintf('handle_%s_filter', $key));
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        });

        if($this->query->getModel()->timestamps){
            return $this->query->latest($this->getTable() . '.created_at');
        }
        return $this->query;
    }

    public function searchWith($scoutModelClass)
    {
        $model = new $scoutModelClass;
        if ($search = $this->request->input('search')) {
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

    protected function getTable() {
        if ($this->tableName) {
            return $this->tableName;
        }

        return $this->tableName = $this->query->getModel()->getTable();
    }
}
