<?php

namespace YS\Datatable;

use YS\Datatable\Traits\HasQueryBuilder;
use YS\Datatable\Traits\ManagesTableColumns;

abstract class AbstractDatatable implements DatatableDriverInterface
{
    use ManagesTableColumns, HasQueryBuilder;

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
            $class = config('datatable.export')[strtolower($this->request->extension())];
            
            $handler = new DatatableExportHandler( $class, $source );

            return $json ?   $handler->response() :  $handler;
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
            $class = config('datatable.export')[strtolower($this->request->extension())];

            $handler = new DatatableExportHandler( $class, $source );

            return $handler->response();
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
    }

    /**
     * Prepare query to fetch result from storage
     *
     * @return bool
     */
    protected function prepareQuery()
    {
        $this->setColumns();

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
        if( $this->request->isSearchable() || !empty($this->searchColumns) )
        {
            $this->totalData = $this->query->getCountForPagination();

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
        if( ! $this->totalData )
        {
             $this->totalData =  $this->query->getCountForPagination();

             $this->totalFiltered =   $this->totalData;
        }
        else
        {
          
            $this->totalFiltered =  $this->query->getCountForPagination();
            if( !$this->totalFiltered )
            {
                if (!empty($this->havingColumns)) 
                {
                    $this->query->bindings['where'] = [];
                    $this->query->wheres = [];
                    $this->havingCondition($this->havingColumns);
		            $this->totalFiltered =  $this->query->getCountForPagination();
                }
            }
        }
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
            $this->query = $this->condition( $this->whereColumns );
        }
    }


    /**
     * Set filterable conditions on query
     *
     * @return void
     */
    protected function setFilters()
    {
        $filters = $this->request->getFilters();
        $this->query = $this->query->where($filters['basic']);
        if( count($filters['array']) > 0 )
        {
            $this->setArrayFilters( $filters['array']);
        }
    }

    /**
     * set array filter conditions on query
     *
     * @param array $filters
     */
    protected function setArrayFilters( array $filters )
    {
        foreach( $filters as $k => $v )
        {
            if( count($v) > 0)
            {
                if (strpos($k, '_at') !== false || strpos($k, 'date') !== false || strpos($k, 'time') !== false) 
                {
                    $this->query = $this->query->whereBetween($k, $v);
                }
                else
                {
                    $this->query = $this->query->whereIn($k, $v);
                }
            }
        }
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
       return response()->json($this->response());
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

