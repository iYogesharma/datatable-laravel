<?php

namespace YS\Datatable\Tests;

use YS\Datatable\Datatable;
use YS\Datatable\QueryBuilder;
use YS\Datatable\Collection;
use YS\Datatable\Eloquent;

use YS\Datatable\Tests\Models\User;

use DB;

class DataTableInitTest extends TestCase
{
    public function test_datatable_helper_return_instance_of_datatable()
    {
       $this->assertTrue(datatable() instanceof Datatable);
    }

    public function test_datatable_can_be_initialized_from_query_builder()
    {
        $this->withoutExceptionHandling();
        
        $table = datatable(User::getQuery());
      
        $this->assertTrue( $table instanceof QueryBuilder);
    }

    public function test_datatable_can_be_initialized_from_eloquent_collection()
    {
        $this->withoutExceptionHandling();

        $table = datatable(User::get());
      
        $this->assertTrue( $table instanceof Collection);
    }

    public function test_datatable_can_be_initialized_from_db_collection()
    {
        $this->withoutExceptionHandling();

        $table = datatable(DB::table('users')->get());
      
        $this->assertTrue( $table instanceof Collection);
    }

    public function test_datatable_can_be_initialized_from_eloquent()
    {
        $this->withoutExceptionHandling();

        $table = datatable(user::select('id','name','email'));
      
        $this->assertTrue( $table instanceof Eloquent);
    }
    
}
