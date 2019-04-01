<?php

if (!function_exists('datatable')) 
{
    /**
     * 
     *  @return instance of \App\Helpers\Datatable  class
     * 
     */
    function datatable($source=null)
    {
        if($source!=null)
        {
            return app('datatable')->make($source);
        }
        return app('datatable');
    }
}

if (!function_exists('table')) 
{
    /**
     * 
     *  @return instance of \App\Helpers\Table  class
     * 
     */
    function table()
    {
        return app('table');
    }
}