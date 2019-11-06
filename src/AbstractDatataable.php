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
     * Represent limit on  no of rows to display
     *
     * @var int
     */
    protected $limit;

    /**
     * Represent row number from where to get data
     *
     * @var int
     */
    protected $start;

    /**
     * Represent index of ordering column
     * @default is first column in columns array
     *
     * @var int
     */
    protected $order = 0;

    /**
     *
     *
     * @var int
     */
    protected $draw;

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
     * @var string
     */
    protected $search;

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
     * Initialize datatable
     * @param  object $source instance of one of 
     * supported driver class 
     *
     * @return object instance of datatabele class 
     * based on input source 
     */
    public function datatable($source , $json = false )
    {
        // Set properties of class and initialize datatable
        $this->boot($source);

        return $json?json_encode($this->response()):$this;
    }
    

    /**
     * Initialize datatable
     * @param  object $source instance of one of 
     * supported driver class 
     *
     * @return jsonResponse
     */
    public function makeDatatable($source)
    {
        // Set properties of class and initialize datatable
        $this->boot($source);

        return json_encode($this->response());
    }

    /**
     * Initialize datatable buy setting all its 
     * properties to be used throughout the 
     * initialization process
     * @param object $source 
     * @access protected
     * 
     * @return void
     */
    protected function boot($source)
    {
        //Set properties of class used by datatable
        $this->setProperties($_GET);

        /** Set properties of instance of class*/
        $this->setQuery($source);
        
    }

    
    
    /*You Must Override this function 
    to set query  based on particular driver class 
    For the datatable to work properly
    */
    
    /**
     * Set  protected properties from request paramenters
     *
     * @param $_GET
     */
    protected function setProperties($parameters)
    {
        $this->draw = $parameters['draw'];
        $this->start = $parameters['start'];
        $this->limit = $parameters['length'];

        //checks if ordering in enabled in datatable or not
        if (isset($parameters['order'])) {
            $this->order = $parameters['order'][0]['column'];
            $this->dir = $parameters['order'][0]['dir'];
        }
        $this->search = $parameters['search']['value'];
        $this->setColumns($parameters['columns']);
        return $this;
    }

    /**
     * Set column names which are displayed on datatables
     *
     * @param array columns of datatable
     */
    protected function setColumns($columns)
    {
        foreach ($columns as $c) {

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
                if (strpos($c, ' as')) {
                    $column = explode(' as', $c)[1];
                    if (in_array(trim($column), $this->searchColumns, true)) {
                        $this->whereColumns[] = explode(' as', $c)[0];
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
        
        if ($this->limit === "-1") {
            $this->prepareResultWithoutOffset();
        } else {
            $this->prepareResultWithOffsetAndOrderBy();
        }

        return true;
    }

    /**
     * Checks whether the query is for searh/filter operation of datatable 
     * if query is for searching tan prepare search query
     * 
     * @return void
     */
    protected function checkIfQueryIsForSearchingPurpose()
    {
        $this->search != null ?  $this->searchQuery() :'';
    }

    /**
     * Prepare result to return as response
     *
     * @return void
     */
    protected function prepareResultWithoutOffset()
    {
        $this->totalData = $this->query->count();
        $this->totalFiltered = $this->totalData;
        $this->result = $this->query->orderBy($this->columns[$this->order], $this->dir)->get();
        $this->query = '';
    }

    /**
     * Prepare result to return as response
     *
     * @return void
     */
    public function prepareResultWithOffsetAndOrderBy()
    {
        $this->totalData = $this->query->count();
        $this->totalFiltered = $this->totalData;
        $this->query = $this->query->offset($this->start)->limit($this->limit)->orderBy($this->columns[$this->order], $this->dir);
        $this->result = $this->query->get();
        $this->query = '';
    }

    /**
     * Prepare result to return as response
     *
     * @return void
     */
    public function prepareResultWithoutOffsetAndOrderBy()
    {
        $this->totalData = $this->query->count();
        $this->totalFiltered = $this->totalData;
        $this->result = $this->query;
        $this->query = '';
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
            $this->query = $this->condition($this->search, $this->whereColumns);
        }

    }

    /**
     * Apply conditions on query
     *
     * @return \Illuminate\Database\Eloquent\Builder instance
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
     * @return \Illuminate\Database\Eloquent\Builder instance
     */
    protected function nestedWheres($q)
    {
        for ($i = 1; $i < count($this->whereColumns); $i++) {
            $q->orWhere($this->whereColumns[$i], 'LIKE', "%{$this->search}%");
        }
        return $q;
    }

    /**
     * Initialise Datatable
     *
     * @return mixed
     */
    public function init()
    {
        return json_encode($this->response());
    }

    /**
     * Return data to initialise datatable
     *
     * @return array
     */
    public function response()
    {
        return [
            "draw" => intval($this->draw),
            "recordsTotal" => intval($this->totalData),
            "recordsFiltered" => intval($this->totalFiltered),
            "data" => $this->result,
        ];

    }

    /**
     * Add/edit column details of datatable
     *
     * @param string column name
     * @param Closure
     *
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
     */
    public function edit($column, Closure $closure)
    {
        return $this->add($column, $closure);
    }

    /**
     * remove column  of datatable
     *
     * @param string/array
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
     * @param string column name
     * @param Closure
     *
     */
    public function addColumns(array $column)
    {
        foreach ($column as $c => $clos) {
            foreach ($this->result as $r) {
                $r->$c = $clos->call($this, $r);
            }

        }
        return $this;
    }

    /**
     * Add/edit  details of multiple columns of datatable
     *
     * @param string column name
     * @param Closure
     *
     */
    public function editColumns(array $column)
    {
        return $this->addColumns($column);
    }
}
