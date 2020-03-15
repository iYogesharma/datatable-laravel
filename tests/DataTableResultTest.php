<?php
    
    namespace YS\Datatable\Tests;
    
    use YS\Datatable\Datatable;
    
    use YS\Datatable\Tests\Models\User;
    
    use DB;
    
    class DataTableResultTest extends TestCase
    {
        public function test_it_should_returns_json_string()
        {
            $data = datatable(User::select('id','name'),true);
            
            $this->assertTrue( gettype($data) === "string");
        }
    
        public function test_it_should_returns_object()
        {
            $data = datatable(User::select('id','name'));
            
            $this->assertTrue( gettype($data) === "object");
        }
        
        public function test_able_to_add_custom_column()
        {
            $table = datatable(User::select('id','name'));
            
            $table->add('active',function(){
                return true;
            });
            
            $column = $table->getResult()[0];
            
            $this->assertTrue( property_exists($column, 'active') );
        }
    
        public function test_able_to_add_multiple_custom_columns()
        {
            $table = datatable( User::select('id','name') );
        
            $table->addColumns( [
                'active' => function() {
                    return true;
                },
                'test' => function() {
                    return "TEST";
                }
            ] );
        
            $column = $table->getResult()[0];
        
            $this->assertTrue( property_exists($column, 'active') );
            
            $this->assertTrue( property_exists($column, 'test') );
        }
        
        public function test_able_to_edit_column()
        {
            $table = datatable(User::select('id','name'));
            
            $table->edit('name',function(){
                return 'Edit';
            });
        
            $column = $table->getResult()[0];
            
            $this->assertTrue( $column->name === 'Edit' );
        }
    
        public function test_able_to_remove_column()
        {
            $table = datatable(User::select('id','name'));
        
            $table->remove('name');
        
            $column = $table->getResult()[0];
        
            $this->assertFalse( property_exists($column, 'name') );
        }
    
        public function test_able_to_remove_multiple_columns()
        {
            $table = datatable( User::select('id','name') );
        
            $table->remove(['name','id']);
        
            $column = $table->getResult()[0];
        
            $this->assertFalse( property_exists($column, 'name') );
    
            $this->assertFalse( property_exists($column, 'id') );
        }
    }
    