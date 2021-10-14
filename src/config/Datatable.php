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
];
