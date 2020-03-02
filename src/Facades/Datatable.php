<?php

namespace YS\Datatable\Facades;


use Illuminate\Support\Facades\Facade;


class Datatable extends Facade
{
    protected static function getFacadeAccessor() 
    {
        return 'datatable'; 
    }
}
