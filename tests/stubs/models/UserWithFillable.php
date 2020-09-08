<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWithFillable extends Model {

    protected $fillable = [
        'name',
        'email',
        'password'
    ];
}