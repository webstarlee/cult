<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cult extends Model
{
    protected $table = 'cults';

    protected $fillable = [
        'cult_name', 'user_id',
    ];
}
