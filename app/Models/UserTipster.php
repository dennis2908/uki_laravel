<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTipster extends Model
{

    protected $table = 'user';

    protected $fillable = [
        'avatar',
        'create_time',
        'name',
        'token',
        'type ',
        'uid ',
        'update_time',
        'platform'
    ];
}
