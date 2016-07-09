<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{

    protected $table = 'trainers';

    protected $fillable = [
        'name', 'email', 'password','active','phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
