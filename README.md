# jQuery Datatables For Laravel 5.x
A simple package to ease datatable.js server side operations

This package is created to handle [server-side](https://www.datatables.net/manual/server-side) works of [DataTables](http://datatables.net) jQuery Plugin via [AJAX option](https://datatables.net/reference/option/ajax) by using Eloquent ORM, Query Builder or Collection.

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
- [Laravel 5.4|5.5|5.6](https://github.com/laravel/framework)
- [jQuery DataTables v1.10.x](http://datatables.net/)



## Quick Installation
```bash
$ composer require iyogesharma/datatable:"dev-master"
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
]
```

## License

The MIT License (MIT). Please see [License File](https://github.com/iYogesharma/datatables/blob/master/LICENSE.md) for more information.
