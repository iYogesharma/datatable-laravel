<?php 

namespace YS\Datatable;


class DatatableExportHandler 
{
    public $handler;

    public function __construct( $handler, $source )
    {
        $this->handler =  (new $handler( $source )); 
    }

    public function init() 
    {
        return  $this->handler->response();
    }

    public function handler() 
    {
        return  $this->handler;
    }

    public function response() 
    {
        return  $this->init();
    }
}