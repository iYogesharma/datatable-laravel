<?php 

namespace YS\Datatable;

use Closure;

class Datatable
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
     * 
     * 
     * @var int
     */
    protected $order;

        /** 
     * 
     * 
     * @var int
     */
    protected $draw;

    /**
     *  Direction of sort asc/desc
     * 
     * @var string
     */
    protected $dir;

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
     * 
     * @param mixed
     */
    public function of($source)
    {
        /** Set properties of instance of class*/
        $this->setProperties($_GET);

        if($this->valid($source))
        {
            return $this;
        }
        throw new \Exception("Data source  must instance of".\Illuminate\Database\Eloquent\Builder);
    }

    /**
     * Initialize datatable
     * 
     * @param mixed
     */
    public function make($source)
    {
        /** Set properties of instance of class*/
        $this->setProperties($_GET);

        if($this->valid($source))
        {
            return json_encode($this->response());
        }
        throw new \Exception("Data source  must be instance of \Illuminate\Database\Eloquent\Builder");
    }

    /**
     * Initialize datatable
     * 
     * @param mixed
     * 
     * @return bool
     */
    public function valid($source)
    {
        if($source instanceof \Illuminate\Database\Eloquent\Builder)
        {
            $this->query =$source->getQuery();
            $this->start(true);
        }
        else if($source instanceof \Illuminate\Database\Eloquent\Collection)
        {

            $this->query =$source;
            $this->start(false);
        }
        else if ($source instanceof \Illuminate\Database\Query\Builder)
        {
            if($source->columns){
                $this->query =$source;
                $this->start(true);
            }
            else
            {
                $this->query =$source->get();
                $this->start(false);
            }
            
        }
        else if ($source instanceof \Illuminate\Support\Collection)
        {
            $this->query=$source;
            $this->start(false);
        }
        else
        {
            return false;
        }
        return true;
    }

    /**
     * Strat process of making query
     * 
     * @param bool decide to prepare query or not
     */
    public function start(bool $query)
    {
        
        if($query)
        {
            $this->setWhereColumns();
            $this->prepareQuery();
        }
        else
        {
            $this->prepareQuery();
        }
    }

    /**
     * Prepare result to return as response 
     * 
     */
    public function prepareResult()
    {
        $this->totalData=$this->query->count();
        $this->totalFiltered=$this->totalData;
        $this->result=$this->query->orderBy($this->columns[$this->order], $this->dir)->get();
        $this->query='';
    }
    
    /**
     * Set column names for where conditions of query
     * 
     * 
     */
    public function setWhereColumns()
    {
        foreach($this->query->columns as $c)
        {
            if(!strpos($c,'_id'))
            {
                if(strpos($c,' as'))
                {
                    $this->whereColumns[]= explode(' as',$c)[0];
                    
                }
                else
                {
                    $this->whereColumns[]=$c;
                }
            }
        }
    }

    /**
     * Set column names which are displayed on datatables
     * 
     * @param array columns of datatable
     */
    public function setColumns($columns)
    {
        foreach($columns as $c)
        {
            $this->columns[]=$c['data'];
        }
    }

    /**
     * Set  protected properties from request paramenters
     * 
     * @param $_GET 
     */
    public function setProperties($parameters) 
    {
        $this->draw=$parameters['draw'];
        $this->start=$parameters['start'];
        $this->limit=$parameters['length'];
        $this->order=$parameters['order'][0]['column'];
        $this->dir=$parameters['order'][0]['dir'];
        $this->search=$parameters['search']['value'];
        $this->setColumns($parameters['columns']);
        return $this;
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
     * Prepare query to fetch result from storage
     * 
     * @return bool
     */
    public function prepareQuery()
    {
        if($this->search!=null)
        {
            $this->searchQuery();
        }
        if($this->limit === "-1")
        {
            $this->prepareResult();
        }
        else
        {
            $this->totalData=$this->query->count();
            $this->totalFiltered=$this->totalData;
            $this->query=$this->query->offset($this->start)->limit($this->limit)->orderBy($this->columns[$this->order], $this->dir);
            $this->result=$this->query->get();
            $this->query='';
        }

        return true;
    }

    /**
     * Handle datatable search operation
     * 
     */
    public function searchQuery() 
    {
        if(!empty($this->columns))
        {
            $this->query=$this->condition($this->search,$this->whereColumns);
        } 
        
    }

    /**
     * Apply conditions on query
     * 
     * @return \Illuminate\Database\Eloquent\Builder instance
     */
    public function condition($search,$columns) 
    {
        return $this->query->where(function ($q) use ($search,$columns){
                                $q->where($columns[0],'LIKE', "%{$search}%");
                                return $this->nestedWheres($q);
                            });
    }

    /**
     * Return all where conditions to be nested
     * 
     * @param mixed $q
     * @return \Illuminate\Database\Eloquent\Builder instance
     */
    public function nestedWheres($q)
    {
        for($i=1;$i<count($this->whereColumns);$i++)
        {
            $q->orWhere($this->whereColumns[$i],'LIKE', "%{$this->search}%");
        }
        return $q;
    }

    /**
     * Return data to initialise datatable
     * 
     * @return array
     */
    public function response()
    {
        return  [
            "draw" => intval($this->draw),
            "recordsTotal" => intval($this->totalData),
            "recordsFiltered" => intval($this->totalFiltered),
            "data" => $this->result
        ];

    }
    
    /**
     * Add/edit column details of datatable
     * 
     * @param string column name
     * @param Closure
     * 
     */
    public function add($column,Closure $closure)
    {
        foreach($this->result as $r){
            $r->$column= $closure->call($this,$r);
        }
        return $this;
    }

    /**
     * Add/edit column details of datatable
     * 
     * @param string column name
     * @param Closure
     */
    public function edit($column,Closure $closure)
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
        if(is_array($column))
        {
            foreach($column as $c)
            {
                foreach($this->result as $r){
                    unset($r->$c);
                }
                
            }
        }
        else
        {
            foreach($this->result as $r){
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
        foreach($column as $c=>$clos)
        {
            foreach($this->result as $r){
                $r->$c= $clos->call($this,$r);
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
