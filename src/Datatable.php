<?php

namespace YS\Datatable;

class Datatable
{
    /**
     * Initialize datatable
     * @param  object $source instance of one of 
     * supported driver class 
     * 
     * @return object
     */
    public function of( $source )
    {
        return $this->make( $source ) ;
    }

    /**
     * Initialize datatable
     * @param  object $source instance of one of 
     * supported driver class 
     * @param bool $json indicate whether to return json or instance
     * 
     * @return object
     */
    public function make( $source , $json = false )
    {
        $drivers = config('datatable.drivers');
        foreach( $drivers as $k=>$v)
        {
            if($source instanceof $k){
                
                return resolve($v)->datatable( $source , $json );
            }
        }
        throw new \Exception("Data source  must be instance of one of the drivers specified in config");
    }

    /**
     * Initialize datatable using Eloquent Builder
     * @param \Illuminate\Database\Eloquent\Builder|mixed $source
     * 
     * @return AbstractDatatable|Eloquent
     */
    public function eloquent( $source ) 
    {
        return (new Eloquent)->create( $source );
    }

     /**
     * Initialize datatable using Collection 
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Supprt\Collection|mixed $source
     * 
     * @return AbstractDatatable|Collection
     */
    public function collection( $source ) 
    {
        return (new Collection)->create( $source );
    }

    /**
     * Initialize datatable using Query Builder 
     * @param \Illuminate\Database\Query\Builder|mixed $source
     * 
     * @return AbstractDatatable|QueryBuilder
     */
    public function queryBuilder( $source ) 
    {
        return (new QueryBuilder)->create( $source );
    }
}
