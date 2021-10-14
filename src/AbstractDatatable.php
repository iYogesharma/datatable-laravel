<?php

namespace YS\Datatable;

use Closure;

abstract class AbstractDatatable implements DatatableDriverInterface
{
    /**
     * Columns of table
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Columns for where conditions
     *
     * @var array
     */
    protected $whereColumns = [];

    /**
     * Searchable Columns for where conditions
     *
     * @var array
     */
    protected $searchColumns = [];

    /**
     * Represent index of ordering column
     * @default is first column in columns array
     *
     * @var int
     */
    protected $order = 0;
    
    /**
     *  Direction of sort asc/desc
     *  @default direction ascending
     *
     *  @var string
     */
    protected $dir = 'asc';

    /**
     * Value to be searched in table
     *
     * @var mixed
     */
    protected $result;

    /**
     * Total data fetched fom storage
     *
     * @var int
     */
    protected $totalData;

    /**
     * Total records filtered
     *
     * @var int
     */
    protected $totalFiltered;

    /**
     * Query to fetch data from storage
     *
     * @var mixed
     */
    protected $query;
    
    /**
     * Holds DatatableRequest Instance
     * @var DatatableRequest
     */
     protected  $request;
    
    /**
     * Initializes new instance
     */
    public function __construct()
    {
        $this->request = new DatatableRequest();
    }
    
    /**
     * Initialize datatable
     * @param  object $source instance of one of
     * supported driver class
     *
     * @return array|mixed|string
     */
    public function datatable($source, $json = false)
    {
        if( $this->request->isForExport() )
        {
            $class = "\\YS\\Export\\".$this->request->extension();
            return (new $class( $source ))->response();
        }
        // Set properties of class and initialize datatable
        $this->boot($source);
       
        return $json ? $this->jsonResponse() : $this;
    }
    
    /**
     * Initialize datatable
     * @param  object $source instance of one of
     * supported driver class
     *
     * @return mixed|string
     */
    public function makeDatatable($source)
    {
        if( $this->request->isForExport() )
        {
            $class = "\\YS\\Export\\".$this->request->extension();
            return (new $class( $source ))->response();
        }
        // Set properties of class and initialize datatable
        $this->boot($source);

        return  $this->jsonResponse();
    }
    
    /**
     * Set @property $query of class
     * @param instance $source
     */
    abstract public function setQuery($source);
    
    /**
     * Initialize datatable buy setting all its
     * properties to be used throughout the
     * initialization process
     * @param object $source
     *
     * @return void
     */
    protected function boot($source)
    {
        //Set properties of class used by datatable
        $this->setProperties();

        /** Set properties of instance of class*/
        $this->setQuery($source);
    }

    /**
     * Set datatable properties
     *
     * @return void
     */
    protected function setProperties()
    {
        //checks if ordering in enabled in datatable or not
        if ( $this->request->isOrderable() ) {
            $this->order = $this->request->getOrderableColumnIndex();
            $this->dir = $this->request->getOrderDirection();
        }
        
        $this->setColumns();
    }

    /**
     * Set column names which are displayed on datatables
     *
     * @return void
     */
    protected function setColumns()
    {
        foreach ($this->request->getColumns() as $c) {

            $this->columns[] = $c['data'];
            if ($c['searchable'] == 'true') {
                $this->searchColumns[] = $c['data'];
            }
        }
    }

    /**
     * Set column names for where conditions of query
     *
     * @return void
     */
    protected function setWhereColumns()
    {
        foreach ($this->query->columns as $c) {
            if (!strpos($c, '_id')) {
                if (strpos($c, ' as ')) {
                    $column = explode(' as ', $c);
                    if (in_array(trim($column[1]), $this->searchColumns, true)) {
                        $this->whereColumns[] = $column[0];
                    }

                } else {
                    if (isset(explode('.', $c)[1])) {
                        if (in_array(explode('.', $c)[1], $this->searchColumns, true)) {
                            $this->whereColumns[] = $c;
                        }

                    } else {
                        $this->whereColumns[] = $c;
                    }
                }
            }
        }
    }

    /**
     * Prepare query to fetch result from storage
     *
     * @return bool
     */
    protected function prepareQuery()
    {
        $this->checkIfQueryIsForSearchingPurpose();
    
        $this->setTotalDataAndFiltered();
        
        if ($this->request->getPerPage() === "-1") {
            $this->prepareQueryWithoutOffset();
        } else {
            $this->prepareQueryWithOffsetAndOrderBy();
        }

        return true;
    }

    /**
     * Checks whether the query is for search/filter operation of datatable
     * if query is for searching tan prepare search query
     *
     * @return void
     */
    protected function checkIfQueryIsForSearchingPurpose()
    {
        if( $this->request->isSearchable() )
        {
            $this->totalData = $this->query->count();
            $this->searchQuery();
        }
    }
    
    /**
     * Set @properties $totalData and $totalFiltered of class
     *
     * @returrn void
     */
    protected function setTotalDataAndFiltered()
    {
        $this->totalData = $this->totalData ?? $this->query->count();
        $this->totalFiltered =  $this->query->count();
    }
    /**
     * Prepare result to return as response
     *
     * @return void
     */
    protected function prepareQueryWithoutOffset()
    {
        $this->query = $this->query->orderBy($this->columns[$this->order],$this->dir);
        
        $this->result = $this->query->get();
    }

    /**
     * Prepare result to return as response
     *
     * @return void
     */
    public function prepareQueryWithOffsetAndOrderBy()
    {
        $this->query = $this->query->offset($this->request->getStart())
                            ->limit($this->request->getPerPage())
                            ->orderBy($this->columns[$this->order],$this->dir);
        $this->result = $this->query->get();
    }

    /**
     * Prepare result to return as response
     *
     * @return void
     */
    public function prepareResultWithoutOffsetAndOrderBy()
    {
        $this->result = $this->query;
    }

    /**
     * Handle datatable search operation
     *
     */
    protected function searchQuery()
    {
        //set columns that are searchable
        $this->setWhereColumns();

        if (!empty($this->whereColumns)) {
            $this->query = $this->condition($this->request->getSearchString(), $this->whereColumns);
        }

    }
    
    /**
     * Apply conditions on query
     * @param string $search
     * @param array $columns
     *
     * @return mixed
     */
    protected function condition($search, $columns)
    {
        return $this->query->where(function ($q) use ($search, $columns) {
            $q->where($columns[0], 'LIKE', "%{$search}%");
            return $this->nestedWheres($q);
        });
    }

    /**
     * Return all where conditions to be nested
     *
     * @param mixed $q
     *
     * @return \Illuminate\Database\Eloquent\Builder instance
     */
    protected function nestedWheres($q)
    {
        for ($i = 1; $i < count($this->whereColumns); $i++) {
            $q->orWhere($this->whereColumns[$i], 'LIKE', "%{$this->request->getSearchString()}%");
        }
        return $q;
    }

    /**
     * Initialise Datatable
     *
     * @return false|string
     */
    public function init()
    {
        return $this->jsonResponse();
    }

    /**
     * Return data to initialise datatable
     *
     * @return array
     */
    public function response()
    {
        return [
            "draw" => intval($this->request->getDraw()),
            "recordsTotal" => intval($this->totalData),
            "recordsFiltered" => intval($this->totalFiltered),
            "data" => $this->result,
        ];

    }
    
    /**
     * Return data to initialise datatable
     *
     * @return false|string
     */
    public function jsonResponse()
    {
       return  json_encode($this->response());
       
    }

    /**
     * Add/edit column details of datatable
     *
     * @param string column name
     * @param Closure
     *
     * @return $this
     */
    public function add($column, Closure $closure)
    {
        foreach ($this->result as $r) {
            $r->$column = $closure->call($this, $r);
        }
        return $this;
    }

    /**
     * Add/edit column details of datatable
     *
     * @param string column name
     * @param Closure
     *
     * @return $this
     */
    public function edit($column, Closure $closure)
    {
        return $this->add($column, $closure);
    }

    /**
     * remove column  of datatable
     *
     * @param string/array
     *
     * @return $this
     */
    public function remove($column)
    {
        if (is_array($column)) {
            foreach ($column as $c) {
                foreach ($this->result as $r) {
                    unset($r->$c);
                }
            }
        } else {
            foreach ($this->result as $r) {
                unset($r->$column);
            }
        }
        return $this;
    }

    /**
     * Add/edit  details of multiple columns of datatable
     *
     * @param array $column
     *
     * @return $this
     */
    public function addColumns(array $column)
    {
        foreach ($column as $c => $cols) {
            foreach ($this->result as $r) {
                $r->$c = $cols->call($this, $r);
            }

        }
        return $this;
    }
    
    /**
     * Add/edit  details of multiple columns of datatable
     *
     * @param array $column
     *
     * @return $this
     */
    public function editColumns(array $column)
    {
        return $this->addColumns($column);
    }
    
    /**
     * Get Datatable query result
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * Get Datatable query builder instance
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }
}
