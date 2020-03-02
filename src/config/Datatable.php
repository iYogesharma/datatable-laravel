<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Drivers
    |--------------------------------------------------------------------------
    |
    | Drivers specify various query methods used by users  for queries.
    | The instance returned by database query must match one of these classes
    | for datatable to work
    |
     */
    "drivers" => [

        '\Illuminate\Database\Eloquent\Builder' => "YS\Datatable\Eloquent",

        '\Illuminate\Database\Eloquent\Collection' => 'YS\Datatable\Collection',

        '\Illuminate\Database\Query\Builder' => 'YS\Datatable\QueryBuilder',

        '\Illuminate\Support\Collection' => 'YS\Datatable\Collection',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Skip
    |--------------------------------------------------------------------------
    |
    | The column names that you don't want to show in the exported file .
    | Default are active and id you can add more columns here or remove
    | if you want to show them on exported file
    |
     */
    
    "skip" => [
        'active',
        'id',
        'password',
        'remember_token',
        'deleted_at'
    ]

];
