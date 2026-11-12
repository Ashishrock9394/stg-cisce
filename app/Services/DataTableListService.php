<?php 

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DataTableListService
{
    protected Builder $query;
    protected array $filters = [];
    protected array $customColumns = [];
    protected array $searchable = [];
    protected array $sortable = [];
    protected bool $enableDefaultOrdering = true;

    public function setQuery(Builder $query)
    {
        $this->query = $query;
        return $this;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    public function setSearchableColumns(array $columns)
    {
        $this->searchable = $columns;
        return $this;
    }

    public function setSortableColumns(array $columns)
    {
        $this->sortable = $columns;
        return $this;
    }

    public function enableDefaultOrdering(bool $enable = true)
    {
        $this->enableDefaultOrdering = $enable;
        return $this;
    }

    public function addCustomColumn($name, \Closure $callback)
    {
        $this->customColumns[$name] = $callback;
        return $this;
    }

    public function render(Request $request)
    {
        if (!isset($this->query)) {
            abort(500, 'Query not set in DataTableListService');
        }

        $draw   = (int) $request->get('draw');
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);

        $recordsTotal = (clone $this->query)->count();

        foreach ($this->filters as $field => $callback) {
            $value = $request->get($field);
            if ($value !== null && $value !== '') {
                $callback($this->query, $value);
            }
        }

        $searchValue = $request->input('search.value');
        if ($searchValue && $this->searchable) {
            $this->query->where(function ($q) use ($searchValue) {
                foreach ($this->searchable as $column) {
                    $q->orWhere($column, 'LIKE', "%{$searchValue}%");
                }
            });
        }
        $recordsFiltered = (clone $this->query)->count();
        

        if($this->enableDefaultOrdering){

            $orderRequests = $request->input('order', []);
            $firstColumnName = $orderRequests[0]['name'] ?? null;

            if (empty($orderRequests) || $firstColumnName === null) {
                $this->query->orderBy('class_order', 'ASC')->orderBy('section_id', 'ASC')
                    ->orderBy(DB::raw('CAST(rollno AS UNSIGNED)'), 'ASC');
            }

            foreach ($orderRequests as $order) {

                $index = $order['column'];
                $dir   = $order['dir'] === 'desc' ? 'desc' : 'asc';
                $name  = $request->input("columns.$index.name");

                if ($name === 'display_classname') {
                   $this->query->orderBy('display_classname', 'ASC');
                    continue;
                }

                if (isset($this->sortable[$name])) {
                    $col = $this->sortable[$name];
                    if ($col instanceof \Illuminate\Database\Query\Expression) {
                        $this->query->orderByRaw($col . " $dir");
                    } else {
                        $this->query->orderBy($col, $dir);
                    }
                }
            } 
        }



        if ($length !== -1) {
            $this->query->skip($start)->take($length);
        }

        $data = $this->query->get();

        foreach ($this->customColumns as $key => $callback) {
            $data->transform(function ($row) use ($key, $callback) {
                $row->{$key} = $callback($row);
                return $row;
            });
        }

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
