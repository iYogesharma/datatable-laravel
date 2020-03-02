<?php

namespace YS\Datatable;

use YS\Datatable\Exceptions\DatatableDriverNotFoundException;

class Datatable
{
    /**
     * Initialize datatable
     * @param  object $source instance of one of 
     * supported driver class
     *
     * @return object
     * @throws DatatableDriverNotFoundException
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
     * @throws DatatableDriverNotFoundException
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
        throw new DatatableDriverNotFoundException("Data source  must be instance of one of the drivers specified in config");
    }
    
    /**
     * Initialize datatable using Eloquent Builder
     * @param $source
     *
     *
     * @return array|mixed|string
     * @throws Exceptions\IncorrectDataSourceException
     */
    public function eloquent( $source ) 
    {
        return (new Eloquent)->create( $source );
    }
    
    /**
     * Initialize datatable using Collection
     * @param $source
     *
     * @return array|mixed|string
     * @throws Exceptions\IncorrectDataSourceException
     */
    public function collection( $source ) 
    {
        return (new Collection)->create( $source );
    }
    
    /**
     * Initialize datatable using Query Builder
     * @param $source
     *
     * @return array|mixed|string
     * @throws Exceptions\IncorrectDataSourceException
     */
    public function query( $source )
    {
        return (new QueryBuilder)->create( $source );
    }
}
