<?php

namespace YS\Datatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The name of table model is associated with.
     *
     * @var string
     */
     protected $table='users';
     protected $relations=['drivers'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Eloquent join between user and driver.
     *
     * @var
     */

     public function driver()
     {
       return $this->belongsTo(Driver::class);
     }

}
