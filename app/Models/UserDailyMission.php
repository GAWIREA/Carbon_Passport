<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDailyMission extends Model
{
    //
    protected $fillable = [
        'user_id',
        'title',
        'progress',
        'target',
        'status',
    ];
}
