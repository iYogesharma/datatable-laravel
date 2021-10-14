<?php

if (!function_exists('datatable'))
{
    /**
     * @param null $source
     * @param bool $json
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function datatable($source=null , $json = false )
    {
        if($source!=null)
        {
            return app('datatable')->make( $source , $json );
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

if(!function_exists('heading_case')) {
    /**
     * Convert string to custom heading case
     * @example contact_number to Contact Number
     * @return string
     */
    function heading_case( string $string , $slug = '_'){
        return \Illuminate\Support\Str::title(str_replace( $slug, ' ', trim($string)));
    }
}


if(!function_exists('delete_key')) {
    /**
     * Convert string to custom heading case
     * @param $array reference to array
     * @param tring $value
     * @return void
     */
    function delete_key( &$array , string $value ){
        
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }
        
    }
}

if(!function_exists('delete_keys')) {
    /**
     * Delete specific keys from array by value
     * @param $array reference to array
     * @param array $values
     * @return string
     */
    function delete_keys( &$array , array $values ){
        
        foreach($values as $value )
        {
            delete_key( $array , $value );
        }
        
    }
}
