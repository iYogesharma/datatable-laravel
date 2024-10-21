<p align="center">
	<img src="https://github.com/iYogesharma/datatable-laravel/blob/master/logo.png" /></p>

[![Latest Stable Version](https://poser.pugx.org/iyogesharma/datatable-laravel/v/stable)](https://packagist.org/packages/iyogesharma/datatable-laravel)
[![Total Downloads](https://poser.pugx.org/iyogesharma/datatable-laravel/downloads)](https://packagist.org/packages/iyogesharma/datatable-laravel)
[![License](https://poser.pugx.org/iyogesharma/datatable-laravel/license)](https://packagist.org/packages/iyogesharma/datatable-laravel)

# jQuery Datatables For Laravel 
A simple package to ease datatable.js server side operations

This package is created to handle [server-side](https://www.datatables.net/manual/server-side) and [client-side](https://www.datatables.net/manual) works of [DataTables](http://datatables.net) jQuery Plugin via [AJAX option](https://datatables.net/reference/option/ajax) by using Eloquent ORM, Query Builder or Collection.


## datatable-laravel 4.x

Version 4.x continues the improvements in version 3.x by introducing some new features

## New

 - Added support for select raw queries
 
 - Auto guess column names if no columns are provided in request
 
 - Auto guess column names if * is provided in column names
 
 - Added support for group by and havig clause

 - Example
    ```php
      echo  datatable(User::select('users.name','users.email','users.contact_no','users.role_id')
        ->selectRaw("
            Max(id) as total
       ")
       ->groupBy('users.name', 'users.email', 'users.contact_no'))->init();
    
      echo   datatable(User::select('users.*'))->init();
    ```


 - Added Support For Data Filtering From Client Side
 - Added Column Wise Search Query Support Using Below Api
 - Example
    ```json 
    {
      "columns": [
        {
          "data": "name",
          "name": "name",
          "searchable": true,
          "orderable": true,
          "search": {
            "value": "",
            "regex": false
          }
        }
      ],
      "start": 0,
      "length": 10,
      "search": {
        "value": "Yoges",
        "regex": false
      },
      "filters": {
        "role_id" : [1,2],// role id in 1,2
        "created_at": [date1, date2], // createde at is between date1 and date2,
        "name": "iyogesh" // where name = iyogesh
      }
    }
    
    ```

## Modified 

Modified datatable function to support server side export to xls,csv and json

You just need to pass 2 new arguments in query-string/body  `export` and ` ext `

if ` export = true ` it will return download file response
for `ext` default value is `xlsx`

```javascript 
    https://datatable-url?export=true&ext=xlsx
```



## Using Helper Function

```php
echo datatable()->of(User::query())->init();
echo datatable()->of(DB::table('users')->join1()->join2()->select(column1,column2,...columnK))->init();
echo datatable()->of(DB::table('users'))->init();
echo datatable()->of(User::all())->init();

echo datatables(User::query());
echo datatables(DB::table('users')->join1()->join2()->select(column1,column2,...columnK));
echo datatables(DB::table('users'));
echo datatables(User::all());
```
## Using Facade 

```php

use Datatable;

echo Datatable::of(User::query())->init();
echo Datatable::of(DB::table('users')->join1()->join2()->select(column1,column2,...columnK))->init();
echo Datatable::of(DB::table('users'))->init();
echo Datatable::of(User::all())->init();

echo Datatable::make(User::query());
echo Datatable::make(DB::table('users')->join1()->join2()->select(column1,column2,...columnK));
echo Datatable::make(DB::table('users'));
echo Datatable::make(User::all());
```

## Add/Edit Column

```php

use Datatable;

echo Datatable::of(User::query())->add(columnName,function($user){
    return "<a href='' id='$user->id'>$user->name</a>";
})->init();
```

##  Using Helper Function


```php

echo datatable()->of(User::query())->add(columnName,function($user){
    return "<a href='' id='$user->id'>$user->name</a>";
})->init();
```
## Add/Edit Multiple Columns

```php

use Datatable;

echo Datatable::of(User::query())->addColumns([columnName1=>function($user){
    return "<a href='' id='$user->id'>$user->name</a>";
},columnName2=>function($user){
    return "<a href='' id='$user->id'>$user->name</a>";
}...])->init();
```

## Remove Column

```php

use Datatable;

echo Datatable::of(User::query())->remove(columnName)->init();
```

## Remove Multiple Columns

```php

use Datatable;

echo Datatable::of(User::query())->remove([columnName1,columnName2,...])->init();
```

## Requirements
- [PHP >= 7.0](http://php.net/)
- [Laravel 7.x](https://github.com/laravel/framework)
- [jQuery DataTables v1.10.x](http://datatables.net/)
- You can check previous release for different version of laravel

## Quick Installation
```bash
$ composer require iyogesharma/datatable-laravel:"~1.0"
```

#### Service Provider & Facade (Optional on Laravel 5.5)
Register provider and facade on your `config/app.php` file.
```php
'providers' => [
    ...,
    YS\Datatable\DatatableServiceProvider::class,
]

'aliases' => [
    ...,
    'Datatable' => YS\Datatable\Facades\Datatable::class,
     "Table"=>YS\\Datatable\\Facades\\Table::class
]
```

## load css files 

before ```</body>``` tag add 

```html

    {{table()->css()}}

```

## load script files 

before  ```</body>```  tag add 

```html

    {{table()->scripts()}}

```


## load Dependencies (css/js)

before  ```</body>```  tag add 

```html

    {{table()->dependencies()}}

```



## Initialize Basic DataTable

In HTMl file inside document .ready function write

```html

    {{table()->basic()}}

```


## Initialize Ajax DataTable

In HTMl file inside document .ready function write

```html

    {{table()->ajax($url,$columns,$configs)}}

```

## Example

```HTML
    {{table()->dependencies()}}

     <script>
        $(document).ready(function(){
                {{ table()->ajax('ddd/ddd',
                    [   'name',
                        'email',
                        'office'
                    ],[
                        'paging'=>'true',
                        'fixedheader'=>'true',
                        'buttons'=>['colvis','copy','csv','print'],
                        'order'=>[[0,'desc']],
                        'lengthMenu'=> [[ 10, 20, 30, 40, 50], [ 10, 20, 30, 40, 50]],
                    ]
                )}}
        })
    </script>
```


## License

The MIT License (MIT). Please see [License File](https://github.com/iYogesharma/datatables/blob/master/LICENSE.md) for more information.
