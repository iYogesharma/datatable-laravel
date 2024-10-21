<?php

namespace YS\Datatable\Traits;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Schema;

trait HasQueryBuilder
{
    /**
     * Query to fetch data from storage
     *
     * @var mixed
     */
    protected $query;

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
     * Columns for having conditions
     *
     * @var array
     */
    protected $havingColumns = [];

    /**
     * Searchable Columns for where conditions
     *
     * @var array
     */
    protected $globalSearchColumns = [];
    
    /**
     * Searchable Columns for where conditions
     *
     * @var array
     */
    protected $searchColumns = [];

    private function identifyQueryColumns()
    {
        $skip  = config('datatable.skip') ?? [];

        if(  empty($this->query->columns)  || $this->query->columns[0] === '*' )
        {
            $this->query->columns = Schema::getColumnListing( $this->query->from );
         
            delete_keys($this->query->columns,  $skip);
        }
        else
        {
            foreach ( $this->query->columns as $k => $c )
            {
                if( gettype($c) == 'string' && strpos($c,'.*'))
                {
                    unset($this->query->columns[$k]);
                    
                    $table = explode('.*',$c)[0];
                    
                    $columns = Schema::getColumnListing( $table );
                    
                    delete_keys($columns, $skip);
                    
                    array_walk($columns, function(&$value)use($table) { $value = "{$table}.{$value}"; } );
                    
                    $this->query->columns = array_merge(
                        $this->query->columns,
                        $columns
                    );
                }
            }
        }

    }

    private function identifyGlobalSerachColumnNames() {

        foreach( $this->queryColumns() as $columnName )
        {
            if(gettype($columnName) != 'string')
            {
                $this->globalSearchColumns[] = $columnName;
            }
            elseif (strpos($columnName, ' as '))
            {
                $column = explode(' as ',$columnName);
                $this->globalSearchColumns[] = $column[1];
            }
            elseif (!strpos($columnName, '_id'))
            {
                if (strpos($columnName, ' as '))
                {
                    $column = explode(' as ',$columnName);
                    $this->globalSearchColumns[] = $column[1];
    
                } 
                else 
                {
                    if (isset(explode('.', $columnName)[1]))
                    {
                        $column = explode('.',$columnName);
                        $this->globalSearchColumns[] = $column[1];
                    } 
                    else 
                    {
                        $this->globalSearchColumns[] = $columnName;
                    }
                }
            }
        }
    }

    /**
     * Set column names which are displayed on datatables
     *
     * @return void
     */
    protected function setColumns()
    {
        $this->identifyQueryColumns();

        if(empty($this->request->getColumns()))
        {
            $this->identifyGlobalSerachColumnNames();
            
            $this->columns  = $this->globalSearchColumns;
        }
        else
        {
            foreach ($this->request->getColumns() as $c) {
                $this->columns[] = $c['data'];
                if ($c['searchable'] == 'true') {
                    $this->globalSearchColumns[] = $c['data'];
                    if( isset($c['search']['value']) && $c['search']['value'] != ''){
                        $this->searchColumns[$c['data']] = $c['search']['value'];
                    }
                }
            }
        }
    }

    /**
     * Convert Select Raq Query String TO array of column names/expression
     * @param string  querySelectString
     * */
    protected function extractColumnNamesFromSelectRawQuery($querySelectString) {
        return array_map(function($column) {
            $column = trim($column);

            if (str_contains($column, '(') && str_contains($column, ')')) {
                $column = new Expression($column);
            }
            return  $column;
        }, explode(',',$querySelectString));
    }

    /**
     * Try to parse queryBuilder object and list all possible column names in array
     * @return array
     * */
    protected function queryColumns()
    {
        $columns = $this->query->columns;

        foreach( $columns as $k => $column )
        {
            $columnName  =  $column;

            if(gettype($column) === 'object')
            {
                $columnName  = $column->getValue($this->query->grammar);

                $rawColumns = $this->extractColumnNamesFromSelectRawQuery($columnName);

                unset($columns[$k]);
                
                $columns = [...$columns, ...$rawColumns];
            }
        }

        return  $columns;
    }

    /**
     * Set column names to form the where conditions of query
     *
     * @return void
     */
    protected function setWhereColumns()
    {
        $columns = $this->queryColumns();

        if( !empty($this->globalSearchColumns) && $this->request->isSearchable())
        {
            $search = $this->request->getSearchString();

            foreach ($columns as $c)
            {
                $this->setWhereColumn( $c, $search );
            }
        }
      
        if( !empty($this->searchColumns))
        {
            $searchColumns = $this->searchColumns;
            $this->searchColumns = [];

            foreach( $columns as  $column )
            {
                $columnName  = $column;
                if(gettype($columnName) === 'object'){  $columnName  = $columnName->getValue($this->query->grammar);}
                foreach ($searchColumns as  $key => $search)
                {
                    if (str_contains( $columnName, $key))
                    {
                        $this->searchColumns[] = ['name' => $column, 'search' => $search];
                    }
                }
            }

            $searchColumns = null;

            foreach( $this->searchColumns as $column )
            {
                $this->setWhereColumn( $column['name'], $column['search'], false );
            }
        }
     
    }

    /**
     * Set query where conditions based on type of column name
     *
     * @return void
     */
    protected function setWhereColumn($columnName, $search, $globalSearch = true)
    {
        if(gettype($columnName) === 'object'){
            $columnName  = $columnName->getValue($this->query->grammar);
            if (strpos($columnName, ' as ')) {
                $column = explode(' as ', $columnName);
                if (!$globalSearch || in_array(trim($column[1]), $this->globalSearchColumns, true)) {
                    $this->havingColumns[] = [trim($column[1]), $search];
                }
            }
        }
        elseif (!strpos($columnName, '_id')) {
            if (strpos($columnName, ' as ')) {
                $column = explode(' as ',$columnName);
                if (!$globalSearch || in_array(trim($column[1]), $this->globalSearchColumns, true)) {
                    $this->whereColumns[] = [trim($column[0]), $search];
                }

            } else {
                if (isset(explode('.', $columnName)[1])) {
                    if (!$globalSearch || in_array(explode('.', $columnName)[1], $this->globalSearchColumns, true)) {
                        $this->whereColumns[] =  [trim($columnName), $search];
                    }
                } else {
                    $this->whereColumns[] = [trim($columnName), $search];
                }
            }
        }
    }

    /**
     * Apply conditions on query
     * @param array $columns
     *
     * @return mixed
     */
    protected function condition( $columns )
    {
        return $this->query->where(function ($q) use ($columns) {
            $q->where($columns[0][0], 'LIKE', "%{$columns[0][1]}%");
            return $this->nestedWheres($q);
        });
    }

    /**
     * Apply having clause on query
     * @param array $columns
     *
     * @return mixed
     */
    protected function havingCondition( $columns )
    {
        $this->query->havingRaw("{$columns[0][0]} LIKE '%{$columns[0][1]}%'");

        $this->nestedHaving();
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
            $q->orWhere($this->whereColumns[$i][0], 'LIKE', "%{$this->whereColumns[$i][1]}%");
        }
        return $q; 
    }

    /**
     * Return all having clauses to be nested
     *
     * @return \Illuminate\Database\Eloquent\Builder instance
     */
    protected function nestedHaving()
    {
        for ($i = 1; $i < count($this->havingColumns); $i++) {
            $this->query->orHavingRaw("{$this->havingColumns[$i][0]} LIKE '%{$this->havingColumns[$i][1]}%'");
        }
    }

}